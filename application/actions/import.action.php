<?php

use \Amateur\Model\Db as Db;

helper('upload');

list($marks, $links, $tags, $users, $marks_tags, $screenshots) =
model(['marks', 'links', 'tags', 'users', 'marks_tags', 'screenshots']);

$feed = helper('feed');

# Globals

$user = authenticated_user();

# Query All Link Ids From User (to use later)
$link_ids = $marks->select('related as id')->where(['author' => $user->id])->fetch_ids();
$reverse_link_ids = array_flip($link_ids);

# Handle File Upload

$uploadFile = file_upload();

# Uncompress / Parse

$compressed_mime_types = array('application/x-gzip', 'application/x-download', 'application/x-tar');
if (in_array($_FILES['file']['type'], $compressed_mime_types)) {
  $xml = '';
  $handle = gzopen($uploadFile, "r");
  while ( $buffer = gzread($handle, 4096) ) {
    $xml .= $buffer;
  }
  gzclose($handle);
  $sxe = simplexml_load_string($xml);
} else {
  $sxe = simplexml_load_file($uploadFile);
}

# Process

$tag_ids = [];

echo '<ul class="importing">';

foreach ($sxe->entry as $entry) {

  $params = [];

  $params['title'] = (string)$entry->title;

  $params['published'] = (string)$entry->published;
  $params['updated'] = (string)$entry->updated;

  $params['tags'] = [];
  $params['private_tags'] = [];

  $params['image'] = $params['related'] = $params['via'] = null;

  // it's the way to use namespace with simplexml
  $bm = $entry->children('http://blogmarks.net/ns/');

  if ($bm->isPrivate && (string)$bm->isPrivate) {
    $params['visibility'] = 1;
  } else {
    $params['visibility'] = 0;
  }

  foreach ($entry->link as $link) {
    if ((string)$link['rel'] == 'enclosure') {
      if ((string)$link['type'] == 'image/png' || (string)$link['type'] == 'image/jpg') {
        $params['image'] = (string)$link['href'];
      }
    }
    if ((string)$link['rel'] == 'related') {
      $params['related'] = (string)$link['href'];
    }
    if ((string)$link['rel'] == 'via') {
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

  echo '<li>';
  echo text($params['title']);

  // try {

    $link = $links->with_url($params['related']);
    if (isset($reverse_link_ids[$link->id])) {
      throw new Exception('Already in your marks', 511);
    }

    # Not needed if Screenshots Table is pre-imported
    if ($params['image']) {
      if (!$screenshots->get_one('link', $link->id)) {
        $screenshots->insert([
          'link'      => $link->id,
          'url'       => $params['image'],
          'created'   => db::date($params['published']),
          'generated' => db::date($params['published']),
          'status'    => 1
        ]);
      }
    }

    $marks->insert([
      'title'       => $params['title'],
      'contentType' => $params['contentType'],
      'content'     => $params['content'],
      'author'      => $user->id,
      'related'     => $link->id,
      'visibility'  => $params['visibility'],
      'published'   => db::date($params['published']),
      'updated'     => db::date($params['updated'])
    ]);

    $mark_id = db::insert_id();

    foreach ($params['tags'] as $tag) {
      $tag = $tags->with_label($tag);
      $tag_ids[] = $tag->id;
      $marks_tags->insert([
        'mark_id'    => $mark_id,
        'tag_id'     => $tag->id,
        'user_id'    => $user->id,
        'link_id'    => $link->id,
        'label'      => $tag->label,
        'isHidden'   => 0,
        'visibility' => $params['visibility'],
      ]);
    }

    foreach ($params['private_tags'] as $tag) {
      $tag = $tags->with_label($tag);
      $tag_ids[] = $tag->id;
      $marks_tags->insert([
        'mark_id'    => $mark_id,
        'tag_id'     => $tag->id,
        'user_id'    => $user->id,
        'link_id'    => $link->id,
        'label'      => $tag->label,
        'isHidden'   => 1,
        'visibility' => $params['visibility']
      ]);
    }


    echo ' - <span style="color:#339900">ok</span>';

  // } catch ( Exception $e ) {

  //   switch( $e->getCode() ) {
  //     case '511':
  //       echo ' - <span style="color:#FF9966">already in your marks</span>';
  //       break;
  //     case '512':
  //       echo ' - <span style="color:#FF9966">invalid content</span>';
  //       break;
  //     default:
  //       echo ' - <span style="color:#ffcccc">' . $e->getCode() . ' : ' . $e->getMessage() . '</span>';
  //       break;
  //   }

  // }

  echo '</li>';

}

echo '</ul>';

# Flush user feeds
$feed->flush("feed_marks");
$feed->flush("feed_marks_my_{$user->id}");
$feed->flush("feed_marks_user_{$user->id}");
$feed->flush("tags_user_{$user->id}_public");
$feed->flush("tags_user_{$user->id}_private");

# Also flush tag feeds
foreach (array_unique($tag_ids) as $tag_id) {
  $feed->flush("feed_marks_tag_{$tag_id}");
  $feed->flush("feed_marks_my_{$user->id}_with_tag_{$tag_id}");
}
