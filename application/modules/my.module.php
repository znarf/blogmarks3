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

elseif (url_is('/my/marks/search')) {
  $query = get_param('query');
  return $query ? redirect('/my/marks/search/' . $query) : redirect('/my/marks');
}

elseif ($matches = url_match('/my/marks/search/*')) {
  $query = set_param('query', urldecode($matches[1]));
  title('My Marks', 'with search ' . strong($query));
  side_title('My', 'Tags with search ' . strong($query));
  $container->marks( model('marks')->private_from_user_search->__use($user, $query, $params) );
  $container->tags( model('tags')->model('tags')->private_search_from_user($user, ['query' => $query]) );
  return render('marks');
}

elseif (url_is('/my/tags/autoupdate')) {
  $query = get_param('query', '');
  side_title('My', 'Tags with search ' . strong($query));
  $params = ['limit' => get_int('limit', 50), 'query' => $query];
  $container->tags( model('tags')->private_search_from_user($user, $params) );
  return partial('tags');
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
