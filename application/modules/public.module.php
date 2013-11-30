<?php

list($target, $container) = helper(['target', 'container']);

title('Public Marks');

if (url_is('/marks')) {
  side_title('Public', 'Tags');
  $container->tags( model('tags')->latests );
  $container->marks( helper('marks')->latests );
  render('marks');
}

elseif ($matches = url_match('/marks/tag/*')) {
  # Multiple Tags
  if (strpos($matches[1], ',')) {
    $tags = explode(',', $matches[1]);
    $tag = $target->tag($tags[0]);
    $tags = array_map(function($slug) { return model('tags')->get_one('label', urldecode($slug)); }, $tags);
    $labels = array_map(function($tag) { return strong($tag); }, $tags);
    title('Public Marks', 'with tags ' . implode(' &amp; ', $labels));
    side_title('Tags', 'related with ' . strong($tag));
    $container->tags( model('tags')->related_with->__use($tag) );
    $container->marks( helper('marks')->with_tags->__use($tags) );
    render('marks');
  }
  # Single Tag
  else {
    $tag = $target->tag($matches[1]);
    title('Public Marks', 'with tag ' . strong($tag->label));
    side_title('Tags', 'related with ' . strong($tag->label));
    $container->tags( model('tags')->related_with->__use($tag) );
    $container->marks( helper('marks')->with_tag->__use($tag) );
    render('marks');
  }
}

elseif ($matches = url_match('/user/*/marks/tag/*')) {
  $user = $target->user($matches[1]);
  $tag = $target->tag($matches[2]);
  title('Public Marks', 'from ' . strong($user->name) . ' with tag ' . strong($tag->label));
  side_title('Tags', 'from ' . strong($user->name) . ' related with ' . strong($tag->label));
  $container->tags( model('tags')->from_user_related_with->__use($user, $tag) );
  $container->marks( helper('marks')->from_user_with_tag->__use($user, $tag) );
  render('marks');
}

elseif ($matches = url_match('/user/*/marks')) {
  $user = $target->user($matches[1]);
  title('Public Marks', 'from ' . strong($user->name));
  side_title('Tags', 'from ' . strong($user->name));
  $container->tags( model('tags')->from_user->__use($user) );
  $container->marks( helper('marks')->from_user->__use($user) );
  render('marks');
}

elseif (url_start_with('/tag/')) {
  redirect('/marks' . url());
}
elseif (url_match('/user/*')) {
  redirect(url() . '/marks');
}
else {
  unknown_url();
}
