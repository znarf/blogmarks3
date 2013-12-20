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
      throw new \amateur\core\exception('Mark already exists.', 400);
    }
    # Insert Mark
    $mark = $this->table('marks')->create([
      'author'      => $user->id,
      'related'     => $link->id,
      'title'       => $params['title'],
      'content'     => $params['description'],
      'visibility'  => $params['visibility']
    ]);
    # Insert Tags
    $this->table('marks_tags')->tag_mark($mark, explode(',', $params['tags']));
    # Index Mark
    $this->feed('marks')->index($mark);
    $this->search('marks')->index($mark);
    # Return
    return $mark;
  }

  function update($mark, $params = [])
  {
    # Get Link
    if ($mark->url != $params['url']) {
      $link = $this->table('links')->with_url($params['url']);
      if ($mark->author->mark_with_link($link)) {
        throw new \amateur\core\exception('Mark already exists.', 400);
      }
    } else {
      $link = $mark->related;
    }
    # Handle Content
    if ($mark->contentType == 'html') {
      $content = \Michelf\Markdown::defaultTransform($params['description']);
    }
    else {
      $content = $params['description'];
    }
    # Update Mark
    $mark = $this->table('marks')->update($mark, [
      'related'     => $link->id,
      'title'       => $params['title'],
      'content'     => $content,
      'visibility'  => $params['visibility']
    ]);
    # Un-index Mark
    $this->feed('marks')->unindex($mark);
    # Update Tags
    $this->table('marks_tags')->tag_mark($mark, explode(',', $params['tags']));
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
    $redis = $this->service('redis')->connection();
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
    # Update Search Index
    $this->search('marks')->unindex_user($user);
  }

  # Collections

  function latests($params = [])
  {
    # With feed backend
    $query = $this->table('marks')->query_latest_ids_and_ts();
    return $this->feed('marks')->query("feed_marks", $query, $params);
    # With search backend
    # return $this->search('marks')->search();
  }

  function with_tag($tag, $params = [])
  {
    # With feed backend
    $query = $this->table('marks')->query_ids_and_ts_with_tag($tag);
    return $this->feed('marks')->query("feed_marks_tag_{$tag->id}", $query, $params);
    # With search backend
    # return $this->search('marks')->search(['tag' => $tag] + $params);
  }

  function with_tags($tags, $params = [])
  {
    # Only available with search backend
    return $this->search('marks')->search(['tags' => $tags] + $params);
  }

  function from_user($user, $params = [])
  {
    # With feed backend
    $query = $this->table('marks')->query_ids_and_ts_from_user($user);
    return $this->feed('marks')->query("feed_marks_user_{$user->id}", $query, $params);
    # With search backend
    # return $this->search('marks')->search(['user' => $user] + $params);
  }

  function from_user_with_tag($user, $tag, $params = [])
  {
    # Only available with search backend
    return $this->search('marks')->search(['user' => $user, 'tag' => $tag] + $params);
  }

  function private_from_user($user, $params = [])
  {
    # With feed backend
    $query = $this->table('marks')->query_ids_and_ts_from_user($user, ['private' => true]);
    return $this->feed('marks')->query("feed_marks_my_{$user->id}", $query, $params);
    # With search backend
    # return $this->search('marks')->search(['user' => $user, 'private' => true] + $params);
  }

  function private_from_user_with_tag($user, $tag, $params = [])
  {
    # With feed backend
    $query = $this->table('marks')->query_ids_and_ts_from_user_with_tag($user, $tag, ['private' => true]);
    return $this->feed('marks')->query("feed_marks_my_{$user->id}_tag_{$tag->id}", $query, $params);
    # With search backend
    # return $this->search('marks')->search(['user' => $user, 'tag' => $tag, 'private' => true] + $params);
  }

  function private_from_user_with_tags($user, $tags, $params = [])
  {
    # Only available with search backend
    return $this->search('marks')->search(['user' => $user, 'tags' => $tags, 'private' => true] + $params);
  }

}
