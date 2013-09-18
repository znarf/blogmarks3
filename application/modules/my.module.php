<?php

domain('my');

section('my');

title('My Marks');

check_authenticated();
$user = authenticated_user();

if (url_is('/my/marks')) {
  tags_title('My', 'Tags');
  helper('container')->tags( model('tags')->private_from_user->__use($user) );
  $app->marks( helper('marks')->private_marks_from_user->__use($user) );
}

elseif ($matches = url_match('/my/marks/tag/*')) {
  if (strpos($matches[1], ',')) {
    $tags = explode(',', $matches[1]);
    $tag = helper('target')->tag($tags[0]);
    $tags = array_map(function($slug) { return model('tags')->get_one('label', urldecode($slug)); }, $tags);
    $labels = array_map(function($tag) { return strong($tag); }, $tags);
    title('My Marks', 'with tags ' . implode(' &amp; ', $labels));
    tags_title('My', 'Tags related with ' . strong($tag));
    helper('container')->tags( model('tags')->private_from_user_related_with->__use($user, $tag) );
    $app->marks( helper('marks')->private_marks_from_user_with_tags->__use($user, $tags) );
  }
  else {
    $tag = helper('target')->tag($matches[1]);
    title('My Marks', 'with tag ' . strong($tag));
    tags_title('My', 'Tags related with ' . strong($tag));
    helper('container')->tags( model('tags')->private_from_user_related_with->__use($user, $tag) );
    $app->marks( helper('marks')->private_marks_from_user_with_tag->__use($user, $tag) );
  }
}

elseif (url_is('/my/tags')) {
  $params = ['limit' => get_int('limit', 100), 'query' => get_param('search', '')];
  ok(view('partials/taglist', ['tags' => model('tags')->private_from_user($user, $params)]));
}

elseif (url_is('/my/tags/autocomplete')) {
  $params = ['limit' => get_int('limit', 10), 'query' => get_param('search', '')];
  $tags = model('tags')->private_from_user($user, $params);
  ok(json_encode(array_map('strval', $tags)));
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
