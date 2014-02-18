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
  return render('marks');
}

elseif ($matches = url_match('/my/marks/tag/*')) {
  $tags = explode(',', $matches[1]);
  $tag = $target->tag($tags[0]);
  # Single Tag
  if (count($tags) == 1) {
    title('My Marks', 'with tag ' . strong($tag));
    $container->marks( model('marks')->private_from_user_with_tag->__use($user, $tag, $params) );
  }
  # Multiple Tags
  else {
    $tags = array_map(function($slug) { return table('tags')->get_one('label', urldecode($slug)); }, $tags);
    $labels = array_map(function($tag) { return strong($tag); }, $tags);
    title('My Marks', 'with tags ' . implode(' &amp; ', $labels));
    $container->marks( model('marks')->private_from_user_with_tags->__use($user, $tags, $params) );
  }
  side_title('My', 'Tags related with ' . strong($tag));
  $container->tags( model('tags')->private_from_user_related_with->__use($user, $tag) );
  return render('marks');
}

elseif (url_is('/my/tags')) {
  $params = ['limit' => get_int('limit', 100), 'query' => get_param('search', '')];
  return partial('taglist', ['tags' => model('tags')->private_search_from_user($user, $params)]);
}

elseif (url_is('/my/tags/autocomplete')) {
  $params = ['limit' => get_int('limit', 10), 'query' => get_param('search', '')];
  $tags = model('tags')->private_search_from_user($user, $params);
  return json(array_map('strval', $tags));
}

elseif (url_start_with('/my/marks')) {
  return module('mark');
}
elseif (url_is('/my/')) {
  return redirect('/my/marks');
}
else {
  return unknown_url();
}
