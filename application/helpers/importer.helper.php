<?php

# Create Anonymous Class

$importer = anonymous_class();

# Dependencies

$amqp = service('amqp');

$redis = service('redis')->connection();

list($marks, $links, $tags, $marks_tags, $screenshots) =
table(['marks', 'links', 'tags', 'marks_tags', 'screenshots']);

# Methods

$importer->start = function($user) use ($amqp, $marks) {
  # User
  $this->user = $user;
  # Query All Link Ids From User (to use later)
  $link_ids = $marks->select('related as id')->where(['author' => $user->id])->fetch_ids();
  $this->reverse_link_ids = array_flip($link_ids);
  # Init Amqp
  $this->amqp_channel = $amqp->channel();
  if ($this->amqp_channel) {
    $this->amqp_channel->queue_declare('marks-index', false, true, false, false);
  }
  # Init Tag Ids
  $this->tag_ids = [];
  # Make it chainable
  return $this;
};

$importer->preload = function($marks_params) use ($links) {
  # Preload links
  $hrefs = array_map(function($params) { return $params['related']; }, $marks_params);
  $links->preload('href', $hrefs);
  $links->get_all('href', $hrefs);
};

$importer->parse = function($file) {
  $marks_params = [];
  $sxe = $this->simplexml($file);
  foreach ($sxe->entry as $entry) {
    $marks_params[] = $this->to_array($entry);
  }
  $this->preload($marks_params);
  return $marks_params;
};

$importer->simplexml = function($file) {
  if (in_array($_FILES['file']['type'], ['application/x-gzip', 'application/x-download', 'application/x-tar'])) {
    $xml = '';
    $handle = gzopen($file, "r");
    while ($buffer = gzread($handle, 4096)) {
      $xml .= $buffer;
    }
    gzclose($handle);
    return simplexml_load_string($xml);
  }
  else {
    return simplexml_load_file($file);
  }
};

$importer->to_array = function($entry) {
  $params = [];

  $params['title']     = (string)$entry->title;
  $params['updated']   = (string)$entry->updated;
  $params['published'] = (string)$entry->published;

  # Visibility
  $bm = $entry->children('http://blogmarks.net/ns/');
  if ($bm->isPrivate && (string)$bm->isPrivate) {
    $params['visibility'] = 1;
  } else {
    $params['visibility'] = 0;
  }

  # Links
  $params['image'] = $params['related'] = $params['via'] = null;
  foreach ($entry->link as $link) {
    if ($link['rel'] == 'related') {
      $params['related'] = (string)$link['href'];
    }
    elseif ($link['rel'] == 'via') {
      $params['via'] = (string)$link['href'];
    }
    elseif ($link['rel'] == 'enclosure') {
      if ($link['type'] == 'image/png' || $link['type'] == 'image/jpg') {
        $params['image'] = (string)$link['href'];
      }
    }
  }

  # Tags
  $params['tags'] = [];
  $params['private_tags'] = [];
  foreach ($entry->category as $category) {
    $scheme = (string)$category['scheme'];
    if ($scheme == 'http://blogmarks.net/tag/' || $scheme == 'http://blogmarks.net/tags/') {
      $params['tags'][] = (string)$category['label'];
    }
    else {
      $params['private_tags'][] = (string)$category['label'];
    }
  }

  # Content
  $params['content'] = null;
  $params['contentType'] = 'text';
  if ($entry->content) {
    $params['content'] = (string)$entry->content;
    $params['contentType'] = (string)$entry->content['type'];
  }

  return $params;
};

$importer->insert = function($params) use ($links) {
  # Get Link
  $link = $links->with_url($params['related']);
  if (isset($this->reverse_link_ids[$link->id])) {
    throw http_error(511, 'Already in your marks');
  }
  # Date acceleration
  if ($params['published'] == $params['updated']) {
    $params['published'] = $params['updated'] = $this->convert_date($params['published']);
  }
  else {
    $params['published'] = $this->convert_date($params['published']);
    $params['updated']   = $this->convert_date($params['updated']);
  }
  # Insert Screenshot
  # (not needed if screenshots table is pre-imported)
  if ($params['image']) {
    $this->insert_screenshot($link, $params['image'], $params['published']);
  }
  # Insert Mark
  $mark_id = $this->insert_mark($this->user->id, $link->id, $params);
  # Insert Tags
  $this->insert_tags($mark_id, $this->user->id, $link->id, $params);
  # Index Mark
  if ($this->amqp_channel) $this->index($mark_id);
  # Return Mark Id
  return $mark_id;
};

$importer->insert_screenshot = function($link, $url, $published) use ($screenshots) {
  if (!$screenshots->get_one('link', $link->id)) {
    $screenshots->insert()->set([
      'link'      => (int)$link->id,
      'url'       => $url,
      'created'   => $published,
      'generated' => $published,
      'status'    => 1
    ])->execute();
  }
};

$importer->insert_mark = function($user_id, $link_id, $params) use ($marks) {
  $marks->insert()->set([
    'title'       => $params['title'],
    'contentType' => $params['contentType'],
    'content'     => $params['content'],
    'author'      => (int)$user_id,
    'related'     => (int)$link_id,
    'visibility'  => $params['visibility'],
    'published'   => $params['published'],
    'updated'     => $params['updated']
  ])->execute();
  return \amateur\model\db::insert_id();
};

$importer->insert_tags = function($mark_id, $user_id, $link_id, $params) use ($tags, $marks_tags) {
  # Create Query
  $marks_tags_query = $marks_tags->insert(['mark_id', 'tag_id', 'user_id', 'link_id', 'label', 'isHidden', 'visibility']);
  # Process Tags
  foreach (['tags' => 0, 'private_tags' => 1] as $key => $isHidden) {
    foreach ($params[$key] as $tag) {
      $tag = $tags->with_label($tag);
      $this->tag_ids[] = $tag->id;
      $marks_tags_query->values[] = [
        (int)$mark_id,
        (int)$tag->id,
        (int)$user_id,
        (int)$link_id,
        $tag->label,
        $isHidden,
        $params['visibility']
      ];
      # sqlite doesn't support multiple values until version 3.7.11
      # (3.7.7.1 bundled with php 5.5)
      if (\amateur\model\db::driver() == 'sqlite') {
        $marks_tags_query->execute();
        $marks_tags_query->values = [];
      }
    }
  }
  # Only execute if values is not empty
  if ($marks_tags_query->values) {
    $marks_tags_query->execute();
  }
};

$importer->index = function($mark_id) use ($amqp) {
  static $count = 0;
  # Inject search index in the message queue
  $message = $amqp->json_message(['action' => 'index', 'mark_id' => $mark_id]);
  $this->amqp_channel->batch_basic_publish($message, '', 'marks-index');
  # Batch by 1000
  $count++;
  if ($count % 1000 == 0) {
    $this->amqp_channel->publish_batch();
  }
};

$importer->finish = function() use ($redis) {
  # Flush Amqp
  if ($this->amqp_channel) {
    $this->amqp_channel->publish_batch();
  }
  if ($redis) {
    # Flush Marks Feeds
    $redis->delete("feed_marks");
    $redis->delete("feed_marks_my_{$this->user->id}");
    $redis->delete("feed_marks_user_{$this->user->id}");
    foreach (array_unique($this->tag_ids) as $tag_id) {
      $redis->delete("feed_marks_tag_{$tag_id}");
      $redis->delete("feed_marks_my_{$this->user->id}_with_tag_{$tag_id}");
    }
    # Flush Tags Feeds
    $redis->delete("tags_public");
    $redis->delete("tags_user_{$this->user->id}_public");
    $redis->delete("tags_user_{$this->user->id}_private");
  }
};

$importer->convert_date = function($string) {
  return db_date($string);
};

return $importer;
