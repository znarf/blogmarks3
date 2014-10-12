<?php

list($target, $container, $sidebar) = helper(['target', 'container', 'sidebar']);

domain('my');

section('friends');

title(_('Friends Marks'));

if (!flag('enable_social_features')) {
  return unknown_url();
}

check_authenticated();
$user = authenticated_user();

$params = request_marks_params();

if (url_is('/my/friends/marks')) {
  $container->marks(
    model('marks')->from_friends->__use($user, $params)
  );
  $sidebar->register(['Active', 'Friends'], function () {
    partial('users', ['users' => helper('related')->active_users]);
  });
  return render('marks');
}

elseif ($matches = url_match('/my/friends/marks/tag/*')) {
  $tags = explode(',', $matches[1]);
  $tag = $target->tag($tags[0]);
  # Single Tag
  if (count($tags) == 1) {
    title(_('Friends Marks'), _('with tag') . ' ' . strong($tag));
    $container->marks( model('marks')->from_friends_with_tag->__use($user, $tag, $params) );
  }
  # Multiple Tags
  else {
    $tags = array_map(function($slug) { return table('tags')->get_one('label', urldecode($slug)); }, $tags);
    $labels = array_map(function($tag) { return strong($tag); }, $tags);
    title(_('Friends Marks'), 'with tags ' . implode(' &amp; ', $labels));
    $container->marks( model('marks')->from_friends_with_tags->__use($user, $tags, $params) );
  }
  return render('marks');
}

elseif (url_is('/my/friends/marks/search')) {
  $query = get_param('query');
  return $query ? redirect("/my/friends/marks/search/{$query}") : redirect('/my/friends/marks');
}

elseif ($matches = url_match('/my/friends/marks/search/*')) {
  $query = set_param('query', urldecode($matches[1]));
  title(_('Friends Marks'), 'with search ' . strong($query));
  $container->marks( model('marks')->from_friends_search->__use($user, $query, $params) );
  return render('marks');
}

return unknown_url();
