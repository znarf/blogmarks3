<?php

$user = authenticated_user();

list($feed, $search) = helper(['feed', 'search']);

# Get all Tag ids. Will be used later to flush related feeds.
$tag_ids = model('marks_tags')->select('DISTINCT tag_id as id')->where(['user_id' => $user->id])->fetch_ids();

# Delete Marks
model('marks')->delete(['author' => $user->id]);

# Delete Marks Tags
model('marks_tags')->delete(['user_id' => $user->id]);

# Flush Memcache (optional)

# Flush Feeds
$feed->flush("feed_marks");
$feed->flush("feed_marks_user_{$user->id}");
$feed->flush("feed_marks_my_{$user->id}");

$feed->flush("tags_public");
$feed->flush("tags_user_{$user->id}_public");
$feed->flush("tags_user_{$user->id}_private");

foreach ($tag_ids as $tag_id) {
  $feed->flush("feed_marks_tag_{$tag_id}");
  $feed->flush("feed_marks_my_{$user->id}_tag_{$tag_id}");
}

$search->unindex_user($user);
