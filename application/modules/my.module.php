<?php

list($target, $container) = helper(['target', 'container']);

domain('my');

section('my');

title('My Marks');

check_authenticated();
$user = authenticated_user();

$params = request_marks_params();

if (url_is('/my/marks')) {
  side_title('My', 'Tags');
  $container->tags( model('tags')->private_from_user->__use($user) );
  $container->marks( model('marks')->private_from_user->__use($user, $params) );
  render('marks');
}

elseif ($matches = url_match('/my/marks/tag/*')) {
  # Multiple Tags
  if (strpos($matches[1], ',')) {
    $tags = explode(',', $matches[1]);
    $tag = $target->tag($tags[0]);
    $tags = array_map(function($slug) { return table('tags')->get_one('label', urldecode($slug)); }, $tags);
    $labels = array_map(function($tag) { return strong($tag); }, $tags);
    title('My Marks', 'with tags ' . implode(' &amp; ', $labels));
    side_title('My', 'Tags related with ' . strong($tag));
    $container->tags( model('tags')->private_from_user_related_with->__use($user, $tag) );
    $container->marks( model('marks')->private_from_user_with_tags->__use($user, $tags, $params) );
    render('marks');
  }
  # Single Tag
  else {
    $tag = $target->tag($matches[1]);
    title('My Marks', 'with tag ' . strong($tag));
    side_title('My', 'Tags related with ' . strong($tag));
    $container->tags( model('tags')->private_from_user_related_with->__use($user, $tag) );
    $container->marks( model('marks')->private_from_user_with_tag->__use($user, $tag, $params) );
    render('marks');
  }
}

elseif (url_is('/my/tags')) {
  $params = ['limit' => get_int('limit', 100), 'query' => get_param('search', '')];
  ok(view('partials/taglist', ['tags' => model('tags')->private_search_from_user($user, $params)]));
}

elseif (url_is('/my/tags/autocomplete')) {
  $params = ['limit' => get_int('limit', 10), 'query' => get_param('search', '')];
  $tags = model('tags')->private_search_from_user($user, $params);
  json(array_map('strval', $tags));
}

elseif (url_start_with('/my/marks')) {
  module('mark');
}
elseif (url_is('/my/')) {
  redirect('/my/marks');
}
else {
  unknown_url();
}
