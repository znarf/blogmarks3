<?php

use
amateur\model\db,
amateur\services\amqp;

list($feed, $search) =
helper(['feed', 'search', 'upload']);

list($marks, $links, $tags, $users, $marks_tags, $screenshots) =
model(['marks', 'links', 'tags', 'users', 'marks_tags', 'screenshots']);

# Init Amqp

$amqp_channel = amqp::channel();

$amqp_channel->queue_declare('marks-index', false, true, false, false);

# Globals

$user = authenticated_user();

# Query All Link Ids From User (to use later)

$link_ids = $marks->select('related as id')->where(['author' => $user->id])->fetch_ids();
$reverse_link_ids = array_flip($link_ids);

# Handle File Upload

$upload_file = file_upload();

# Uncompress / Parse

$compressed_mime_types = array('application/x-gzip', 'application/x-download', 'application/x-tar');
if (in_array($_FILES['file']['type'], $compressed_mime_types)) {
  $xml = '';
  $handle = gzopen($upload_file, "r");
  while ($buffer = gzread($handle, 4096 ) {
    $xml .= $buffer;
  }
  gzclose($handle);
  $sxe = simplexml_load_string($xml);
} else {
  $sxe = simplexml_load_file($upload_file);
}

# Process

$tag_ids = [];

echo '<ul class="importing">';

$marks_params = [];

foreach ($sxe->entry as $entry) {

  $params = [];

  $params['title'] = (string)$entry->title;

  $params['published'] = (string)$entry->published;
  $params['updated'] = (string)$entry->updated;

  $params['tags'] = [];
  $params['private_tags'] = [];

  $params['image'] = $params['related'] = $params['via'] = null;

  $bm = $entry->children('http://blogmarks.net/ns/');

  if ($bm->isPrivate && (string)$bm->isPrivate) {
    $params['visibility'] = 1;
  } else {
    $params['visibility'] = 0;
  }

  foreach ($entry->link as $link) {
    $rel = (string)$link['rel'];
    if ($rel == 'enclosure') {
      if ((string)$link['type'] == 'image/png' || (string)$link['type'] == 'image/jpg') {
        $params['image'] = (string)$link['href'];
      }
    }
    elseif ($rel == 'related') {
      $params['related'] = (string)$link['href'];
    }
    elseif ($rel == 'via') {
      $params['via'] = (string)$link['href'];
    }
  }

  foreach ($entry->category as $category) {
    $scheme = (string)$category['scheme'];
    $label = (string)$category['label'];
    if ($scheme == 'http://blogmarks.net/tag/' || $scheme == 'http://blogmarks.net/tags/') {
      $params['tags'][] = $label;
    } else {
      $params['private_tags'][] = $label;
    }
  }

  $params['content'] = null;
  $params['contentType'] = 'text';

  if ($entry->content) {
    if ($entry->content['type']) {
      $params['contentType'] = (string)$entry->content['type'];
    }
    $params['content'] = (string)$entry->content;
  }

  $marks_params[] = $params;

}

# Preload links

$hrefs = array_map(function($params) { return $params['related']; }, $marks_params);
$links->preload('href', $hrefs);
$links->get_all('href', $hrefs);

$count = 0;
foreach ($marks_params as $params) {

  echo '<li>';
  echo text($params['title']);

  try {

    $link = $links->with_url($params['related']);
    if (isset($reverse_link_ids[$link->id])) {
      throw new exception('Already in your marks', 511);
    }

    # Not needed if screenshots table is pre-imported
    if ($params['image']) {
      if (!$screenshots->get_one('link', $link->id)) {
        $screenshots->insert()->set([
          'link'      => (int)$link->id,
          'url'       => $params['image'],
          'created'   => db::date($params['published']),
          'generated' => db::date($params['published']),
          'status'    => 1
        ])->execute();
      }
    }

    $marks->insert()->set([
      'title'       => $params['title'],
      'contentType' => $params['contentType'],
      'content'     => $params['content'],
      'author'      => (int)$user->id,
      'related'     => (int)$link->id,
      'visibility'  => $params['visibility'],
      'published'   => db::date($params['published']),
      'updated'     => db::date($params['updated'])
    ])->execute();

    $mark_id = db::insert_id();

    $mt_query = $marks_tags->insert(['mark_id', 'tag_id', 'user_id', 'link_id', 'label', 'isHidden', 'visibility']);

    foreach ($params['tags'] as $tag) {
      $tag = $tags->with_label($tag);
      $tag_ids[] = $tag->id;
      $mt_query->values[] = [
        (int)$mark_id,
        (int)$tag->id,
        (int)$user->id,
        (int)$link->id,
        $tag->label,
        0,
        $params['visibility']
      ];
      /*
      $marks_tags->insert([
        'mark_id'    => (int)$mark_id,
        'tag_id'     => (int)$tag->id,
        'user_id'    => (int)$user->id,
        'link_id'    => (int)$link->id,
        'label'      => $tag->label,
        'isHidden'   => 0,
        'visibility' => $params['visibility'],
      ]);
      */
    }

    foreach ($params['private_tags'] as $tag) {
      $tag = $tags->with_label($tag);
      $tag_ids[] = $tag->id;
      $mt_query->values[] = [
        (int)$mark_id,
        (int)$tag->id,
        (int)$user->id,
        (int)$link->id,
        $tag->label,
        1,
        $params['visibility']
      ];
      /*
      $marks_tags->insert([
        'mark_id'    => (int)$mark_id,
        'tag_id'     => (int)$tag->id,
        'user_id'    => (int)$user->id,
        'link_id'    => (int)$link->id,
        'label'      => $tag->label,
        'isHidden'   => 1,
        'visibility' => $params['visibility']
      ]);
      */
    }

    # Only execute if values is not empty
    if ($mt_query->values) $mt_query->execute();

    $message = amqp::json_message(['action' => 'index', 'mark_id' => $mark_id]);
    $amqp_channel->batch_basic_publish($message, '', 'marks-index');
    # Batch by 1000
    $count++;
    if ($count % 1000 == 0) $amqp_channel->publish_batch();

    echo ' - <span style="color:#339900">ok</span>';

  } catch ( Exception $e ) {

    switch( $e->getCode() ) {
      case '511':
        echo ' - <span style="color:#FF9966">already in your marks</span>';
        break;
      case '512':
        echo ' - <span style="color:#FF9966">invalid content</span>';
        break;
      default:
        echo ' - <span style="color:#ffcccc">' . $e->getCode() . ' : ' . $e->getMessage() . '</span>';
        break;
    }

  }

  echo '</li>';

}

echo '</ul>';

# Inject AMQP exit (development only)
$message = amqp::json_message(['action' => 'exit']);
$amqp_channel->batch_basic_publish($message, '', 'marks-index');

# Flush AMQP
$amqp_channel->publish_batch();

# Flush user feeds
$feed->flush("feed_marks");
$feed->flush("feed_marks_my_{$user->id}");
$feed->flush("feed_marks_user_{$user->id}");

$feed->flush("tags_public");
$feed->flush("tags_user_{$user->id}_public");
$feed->flush("tags_user_{$user->id}_private");

# Also flush tag feeds
foreach (array_unique($tag_ids) as $tag_id) {
  $feed->flush("feed_marks_tag_{$tag_id}");
  $feed->flush("feed_marks_my_{$user->id}_with_tag_{$tag_id}");
}

