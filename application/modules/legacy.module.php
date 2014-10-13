<?php

if ($matches = url_match('/my/marks/mixed-tag/*')) {
  check_authenticated();
  $user = authenticated_user();
  $params = request_marks_params();
  # Get Tags
  $tags = explode(',', $matches[1]);
  # Public Tag
  $public_tag = table('tags')->get_one('label', urldecode($tags[0]));
  # Private Tag
  $row = table('tags')->fetch_one(['label' => urldecode($tags[0]), 'author' => $user->id]);
  $private_tag = table('tags')->to_object($row);
  # Marks
  title(_('My Marks'), _('with tag') . ' ' . strong($public_tag));
  # Tags
  helper('sidebar')->register(['My', 'Tags related with ' . strong($public_tag)], function() use($user, $public_tag, $private_tag) {
    $tags = [];
    $tags += model('tags')->private_from_user_related_with($user, $public_tag);
    $tags += model('tags')->private_from_user_related_with($user, $private_tag);
    partial('tags', ['tags' => $tags]);
  });
  # Fetch results from both tags
  $results = [];
  foreach ([$public_tag, $private_tag] as $tag) {
    $query = table('marks')->query_ids_and_ts_from_user_with_tag($user, $tag, ['private' => true]);
    $results += feed('marks')->ids_and_ts("feed_marks_my_{$user->id}_tag_{$tag->id}", $query, $params);
  }
  # Soft offset/limit
  if ($params['limit'] > 0) {
    $results = array_slice($results, $params['offset'], $params['limit'] + 1, true);
  }
  helper('container')->marks( feed('marks')->prepare_items($results, $params) );
  return render('marks');
}
