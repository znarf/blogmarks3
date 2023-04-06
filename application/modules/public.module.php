<?php

list($target, $container, $sidebar) = helper(['target', 'container', 'sidebar']);

title(_('Public Marks'));

$params = request_marks_params();

if (url_is('/marks')) {
  $container->marks(
    model('marks')->latests->__use($params)
  );
  $sidebar->register(['Public', 'Tags'], function() {
    partial('tags', ['tags' => model('tags')->latests]);
  });
  $sidebar->register(['Active', 'Users'], function() {
    partial('users', ['users' => helper('related')->active_users]);
  });
  return render('marks');
}

elseif ($matches = url_match('/marks/tag/*')) {
  $tags = explode(',', $matches[1]);
  $tag = $target->tag($tags[0]);
  # Single Tag
  if (count($tags) == 1) {
    title(_('Public Marks'), 'with tag ' . strong($tag));
    $container->marks( model('marks')->with_tag->__use($tag, $params) );
  }
  # Multiple Tags
  else {
    $tags = array_map(function($slug) { return table('tags')->get_one('label', urldecode($slug)); }, $tags);
    $labels = array_map(function($tag) { return strong($tag); }, $tags);
    title(_('Public Marks'), 'with tags ' . implode(' &amp; ', $labels));
    $container->marks( model('marks')->with_tags->__use($tags, $params) );
  }
  $container->tags(
    model('tags')->related_with->__use($tag)
  );
  $sidebar->register(['Tags', 'related with ' . strong($tag)], function() {
    partial('tags');
  });
  $sidebar->register(['Active', 'Users with tag ' . strong($tag)], function() {
    partial('users', ['users' => helper('related')->active_users]);
  });
  return render('marks');
}

elseif ($matches = url_match('/user/*/marks/tag/*')) {
  $user = $target->user($matches[1]);
  $tags = explode(',', $matches[2]);
  $tag = $target->tag($tags[0]);
  # Single Tag
  if (count($tags) == 1) {
    title(_('Public Marks'), 'from ' . strong($user) . ' with tag ' . strong($tag));
    $container->marks( model('marks')->from_user_with_tag->__use($user, $tag, $params) );
  }
  # Multiple Tags
  else {
    $tags = array_map(function($slug) { return table('tags')->get_one('label', urldecode($slug)); }, $tags);
    $labels = array_map(function($tag) { return strong($tag); }, $tags);
    title(_('Public Marks'), 'from ' . strong($user) . 'with tags ' . implode(' &amp; ', $labels));
    $container->marks( model('marks')->from_user_with_tags->__use($user, $tags, $params) );
  }
  $container->tags( model('tags')->from_user_related_with->__use($user, $tag) );
  $sidebar->register(['Tags', 'from ' . strong($user) . ' related with ' . strong($tag)], function() { partial('tags'); });
  return render('marks');
}

elseif ($matches = url_match('/user/*/marks')) {
  $user = $target->user($matches[1]);
  title(_('Public Marks'), 'from ' . strong($user));
  $container->tags( model('tags')->from_user->__use($user) );
  $container->marks( model('marks')->from_user->__use($user, $params) );
  $sidebar->register(['Tags', 'from ' . strong($user)], function() { partial('tags'); });
  return render('marks');
}

elseif (url_is('/marks/search')) {
  $query = get_param('query');
  return $query ? redirect('/marks/search/' . $query) : redirect('/marks');
}

elseif ($matches = url_match('/marks/search/*')) {
  $query = set_param('query', urldecode($matches[1]));
  title(_('Public Marks'), 'with search ' . strong($query));
  $container->marks( model('marks')->public_search->__use($query, $params) );
  $container->tags( model('tags')->public_search->__use(['query' => $query]) );
  $sidebar->register(['Public', 'Tags with search ' . strong($query)], function() { partial('tags'); });
  return render('marks');
}

elseif (url_start_with('/tag/')) {
  return redirect('/marks' . request_url());
}
elseif (url_match('/user/*')) {
  return redirect(request_url() . '/marks');
}
else {
  return unknown_url();
}
