<?php namespace blogmarks\model;

class marks
{

  use
  \amateur\magic\closurable_methods,
  \blogmarks\magic\registry;

  # C-UD

  function create($user, $params = [])
  {
    # Get Link
    $link = $this->table('links')->with_url($params['url']);
    if ($user->mark_with_link($link)) {
      throw new \amateur\exception('Mark already exists.', 400);
    }
    # Handle Content (default to markdown/html)
    $content = \Michelf\Markdown::defaultTransform($params['description']);
    $contentType = 'html';
    # Insert Mark
    $mark = $this->table('marks')->create([
      'author'      => $user->id,
      'related'     => $link->id,
      'title'       => $params['title'],
      'visibility'  => $params['visibility'],
      'content'     => $content,
      'contentType' => $contentType,
    ]);
    # Insert Tags
    $this->table('marks_tags')->tag_mark($mark,
      explode(',', $params['tags']), explode(',', $params['private_tags'])
    );
    # Ensure a screenshot entry exists
    $this->table('screenshots')->ensure_entry_exists_for_mark($mark);
    # Index Mark
    $this->feed('marks')->index($mark);
    $this->search('marks')->index($mark);
    # TODO: Index Tags
    # Return
    return $mark;
  }

  function update($mark, $params = [])
  {
    # Get Link
    if ($mark->url != $params['url']) {
      $link = $this->table('links')->with_url($params['url']);
      if ($mark->author->mark_with_link($link)) {
        throw new \amateur\exception('Mark already exists.', 400);
      }
    } else {
      $link = $mark->related;
    }
    # Handle Content (default to markdown/html)
    $content = \Michelf\Markdown::defaultTransform($params['description']);
    $contentType = 'html';
    # Update Mark
    $mark = $this->table('marks')->update($mark, [
      'related'     => $link->id,
      'title'       => $params['title'],
      'visibility'  => $params['visibility'],
      'content'     => $content,
      'contentType' => $contentType,
    ]);
    # Un-index Mark
    $this->feed('marks')->unindex($mark);
    # Update Tags
    $this->table('marks_tags')->tag_mark($mark,
      explode(',', $params['tags']), explode(',', $params['private_tags'])
    );
    # Re-index Mark
    $this->feed('marks')->index($mark);
    $this->search('marks')->index($mark);
    # Return
    return $mark;
  }

  function delete($mark)
  {
    # Un-index Mark (tags might be unavailable after mark is deleted)
    $this->feed('marks')->unindex($mark);
    $this->search('marks')->unindex($mark);
    # Delete Tags
    $this->table('marks_tags')->delete(['mark_id' => $mark->id]);
    # Delete Mark
    $this->table('marks')->delete($mark);
  }

  function delete_from_user($user)
  {
    # Get all Tag ids. Will be used later to flush related feeds.
    $tag_ids = $this
      ->table('marks_tags')
      ->select('DISTINCT tag_id as id')
      ->where(['user_id' => $user->id])
      ->fetch_ids();
    # Delete Marks
    $this->table('marks')->delete(['author' => $user->id]);
    # Delete Marks Tags
    $this->table('marks_tags')->delete(['user_id' => $user->id]);
    # Flush Feeds
    if ($redis = $this->service('redis')->connection()) {
      # Flush Marks Feeds
      $redis->delete("feed_marks");
      $redis->delete("feed_marks_user_{$user->id}");
      $redis->delete("feed_marks_my_{$user->id}");
      foreach ($tag_ids as $tag_id) {
        $redis->delete("feed_marks_tag_{$tag_id}");
        $redis->delete("feed_marks_my_{$user->id}_tag_{$tag_id}");
      }
      # Flush Tags Feeds
      $redis->delete("tags_public");
      $redis->delete("tags_user_{$user->id}_public");
      $redis->delete("tags_user_{$user->id}_private");
    }
    # Update Search Index
    $this->search('marks')->unindex_user($user);
  }

  # Utility

  function search_with_query($query, $params = [])
  {
    $ids_and_ts = $query
      ->select('m.id, UNIX_TIMESTAMP(m.published) as ts')
      ->limit($params['limit'] + 1)
      ->order_by('published DESC')
      ->fetch_key_values('id', 'ts');

    # Next?
    if (count($ids_and_ts) > $params['limit']) {
      $next = array_pop($ids_and_ts);
    } else {
      $next = null;
    }

    # Total
    $total = null;

    # Items
    $items = $this->table('marks')->get(array_keys($ids_and_ts));

    return compact('params', 'total', 'next', 'items');
  }

  # Collections

  function latests($params = [])
  {
    # With feed backend
    $query = $this->table('marks')->query_latest_ids_and_ts;
    return $this->feed('marks')->query("feed_marks", $query, $params);
    # With search backend
    # return $this->search('marks')->search();
  }

  function with_tag($tag, $params = [])
  {
    # With feed backend
    $query = $this->table('marks')->query_ids_and_ts_with_tag->__use($tag);
    return $this->feed('marks')->query("feed_marks_tag_{$tag->id}", $query, $params);
    # With search backend
    # return $this->search('marks')->search(['tag' => $tag] + $params);
  }

  function with_tags($tags, $params = [])
  {
    if (!$this->search('marks')->available()) {
      # With feed backend (but with multiple queries and soft intersect)
      # This is a bit bloat but what we have to support installs without Elasticsearch
      # And performance is not a requirement in this case.
      foreach ($tags as $tag) {
        $query = $this->table('marks')->query_ids_and_ts_with_tag->__use($tag);
        $tag_results = $this->feed('marks')->ids_and_ts(null, $query, ['limit' => -1] + $params);
        $results = isset($results) ? array_intersect_key($results, $tag_results) : $tag_results;
      }
      # Soft offset/limit
      if ($params['limit'] > 0) {
        $results = array_slice($results, $params['offset'], $params['limit'] + 1, true);
      }
      # Result
      return $this->feed('marks')->prepare_items($results, $params);
    }
    # With search backend
    return $this->search('marks')->search(['tags' => $tags] + $params);
  }

  function from_user($user, $params = [])
  {
    # With feed backend
    $query = $this->table('marks')->query_ids_and_ts_from_user->__use($user);
    return $this->feed('marks')->query("feed_marks_user_{$user->id}", $query, $params);
    # With search backend
    # return $this->search('marks')->search(['user' => $user] + $params);
  }

  function from_user_with_tag($user, $tag, $params = [])
  {
    if (!$this->search('marks')->available()) {
      # With feed backend (but no cache)
      $query = $this->table('marks')->query_ids_and_ts_from_user_with_tag->__use($user, $tag, ['private' => false]);
      return $this->feed('marks')->query(null, $query, $params);
    }
    # With search backend
    return $this->search('marks')->search(['user' => $user, 'tag' => $tag] + $params);
  }

  function from_user_with_tags($user, $tags, $params = [])
  {
    if (!$this->search('marks')->available()) {
      # With feed backend (but with multiple queries and soft intersect)
      # This is a bit bloat but what we have to support installs without Elasticsearch
      # And performance is not a requirement in this case.
      foreach ($tags as $tag) {
        $query = $this->table('marks')->query_ids_and_ts_from_user_with_tag->__use($user, $tag, ['private' => false]);
        $tag_results = $this->feed('marks')->ids_and_ts(null, $query, ['limit' => -1] + $params);
        $results = isset($results) ? array_intersect_key($results, $tag_results) : $tag_results;
      }
      # Soft offset/limit
      if ($params['limit'] > 0) {
        $results = array_slice($results, $params['offset'], $params['limit'] + 1, true);
      }
      # Result
      return $this->feed('marks')->prepare_items($results, $params);
    }
    # With search backend
    return $this->search('marks')->search(['user' => $user, 'tags' => $tags] + $params);
  }

  function private_from_user($user, $params = [])
  {
    # With feed backend
    $query = $this->table('marks')->query_ids_and_ts_from_user->__use($user, ['private' => true]);
    return $this->feed('marks')->query("feed_marks_my_{$user->id}", $query, $params);
    # With search backend
    # return $this->search('marks')->search(['user' => $user, 'private' => true] + $params);
  }

  function private_from_user_with_tag($user, $tag, $params = [])
  {
    # With feed backend
    $query = $this->table('marks')->query_ids_and_ts_from_user_with_tag->__use($user, $tag, ['private' => true]);
    return $this->feed('marks')->query("feed_marks_my_{$user->id}_tag_{$tag->id}", $query, $params);
    # With search backend
    # return $this->search('marks')->search(['user' => $user, 'tag' => $tag, 'private' => true] + $params);
  }

  function private_from_user_with_tags($user, $tags, $params = [])
  {
    if (!$this->search('marks')->available()) {
      # With feed backend (but with multiple queries and soft intersect)
      # This is a bit bloat but what we have to support installs without Elasticsearch
      # And performance is not a requirement in this case.
      foreach ($tags as $tag) {
        $query = $this->table('marks')->query_ids_and_ts_from_user_with_tag->__use($user, $tag, ['private' => true]);
        $tag_results = $this->feed('marks')->ids_and_ts("feed_marks_my_{$user->id}_tag_{$tag->id}", $query, ['limit' => -1] + $params);
        $results = isset($results) ? array_intersect_key($results, $tag_results) : $tag_results;
      }
      # Soft offset/limit
      if ($params['limit'] > 0) {
        $results = array_slice($results, $params['offset'], $params['limit'] + 1, true);
      }
      # Result
      return $this->feed('marks')->prepare_items($results, $params);
    }
    # With search backend
    return $this->search('marks')->search(['user' => $user, 'tags' => $tags, 'private' => true] + $params);
  }

  function private_from_user_search($user, $search, $params = [])
  {
    # Sub-optimal search to support installs without Elasticsearch
    if (!$this->search('marks')->available()) {
      $query = $this
        ->table('marks')
        ->query_ids_and_ts_from_user_search($user, $search, $params + ['private' => true]);
      return $this->search_with_query($query, $params);
    }

    # With search backend
    return $this->search('marks')->search(['user' => $user, 'query' => $search, 'private' => true] + $params);
  }

  function public_search($search, $params = [])
  {
    # Sub-optimal search to support installs without Elasticsearch
    if (!$this->search('marks')->available()) {
      $query = $this
        ->table('marks')
        ->query_ids_and_ts_search_public($search, $params);
      return $this->search_with_query($query, $params);
    }

    # With search backend
    return $this->search('marks')->search(['query' => $search] + $params);
  }

  function from_friends($user, $params = [])
  {
    # With feed backend
    $query = $this->table('marks')->query_ids_and_ts_from_friends->__use($user, ['limit' => 1000]);
    return $this->feed('marks')->query("feed_marks_friends_{$user->id}", $query, $params);
    # With search backend
    return $this->search('marks')->search(['user_ids' => $user->following_ids] + $params);
  }

  function from_friends_with_tag($user, $tag, $params = [])
  {
    # Only available with search backend
    return $this->search('marks')->search(['tag' => $tag, 'user_ids' => $user->following_ids] + $params);
  }

  function from_friends_with_tags($user, $tags, $params = [])
  {
    # Only available with search backend
    return $this->search('marks')->search(['tags' => $tags, 'user_ids' => $user->following_ids] + $params);
  }

  function from_friends_search($user, $query, $params = [])
  {
    # Only available with search backend
    return $this->search('marks')->search(['query' => $query, 'user_ids' => $user->following_ids] + $params);
  }

}
