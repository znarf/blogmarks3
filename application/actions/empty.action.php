<?php

$feed = helper('feed');

$user = authenticated_user();

# Get all Tag ids. Will be used later to flush related feeds.
$query = "SELECT DISTINCT tag_id FROM " . model('marks-tags')->tablename .
         " WHERE " . db_build_where(['user_id' => $user->id]);
$tag_ids = db_fetch_ids(db_query($query), 'tag_id');

# Delete Marks
model('marks')->delete_where(['author' => $user->id]);

# Delete Marks Tags
model('marks-tags')->delete_where(['user_id' => $user->id]);

# Flush Memcache (optional)

# Flush Feeds
$feed->flush("feed_marks");
$feed->flush("feed_marks_user_{$user->id}");
$feed->flush("feed_marks_my_{$user->id}");
foreach ($tag_ids as $tag_id) {
  $feed->flush("feed_marks_tag_{$tag_id}");
  $feed->flush("feed_marks_my_{$user->id}_tag_{$tag_id}");
}
