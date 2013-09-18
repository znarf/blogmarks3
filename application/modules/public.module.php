<?php

// return module('public', function($req, $res) use($app) {

list($target, $container) = helper(['target', 'container']);

title('Public Marks');

if (url_is('/marks')) {
  tags_title('Public', 'Tags');
  $container->tags( model('tags')->latests );
  $app->marks( helper('marks')->latest_marks );
}

elseif ($matches = url_match('/marks/tag/*')) {
  if (strpos($matches[1], ',')) {
    $tags = explode(',', $matches[1]);
    $tag = helper('target')->tag($tags[0]);
    $tags = array_map(function($slug) { return model('tags')->get_one('label', urldecode($slug)); }, $tags);
    $labels = array_map(function($tag) { return strong($tag); }, $tags);
    title('Public Marks', 'with tags ' . implode(' &amp; ', $labels));
    tags_title('Tags', 'related with ' . strong($tag));
    helper('container')->tags( model('tags')->related_with->__use($tag) );
    $app->marks( helper('marks')->marks_with_tags->__use($tags) );
  }
  else {
    $tag = $target->tag($matches[1]);
    title('Public Marks', 'with tag ' . strong($tag->label));
    tags_title('Tags', 'related with ' . strong($tag->label));
    $container->tags( model('tags')->related_with->__use($tag) );
    $app->marks( helper('marks')->marks_with_tag->__use($tag) );
  }
}

elseif ($matches = url_match('/user/*/marks/tag/*')) {
  $user = $target->user($matches[1]);
  $tag = $target->tag($matches[2]);
  title('Public Marks', 'from ' . strong($user->name) . ' with tag ' . strong($tag->label));
  tags_title('Tags', 'from ' . strong($user->name) . ' related with ' . strong($tag->label));
  $container->tags( model('tags')->from_user_related_with->__use($user, $tag) );
  $app->marks( helper('marks')->marks_from_user_with_tag->__use($user, $tag) );
}

elseif ($matches = url_match('/user/*/marks')) {
  $user = $target->user($matches[1]);
  title('Public Marks', 'from ' . strong($user->name));
  tags_title('Tags', 'from ' . strong($user->name));
  $container->tags( model('tags')->from_user->__use($user) );
  $app->marks( helper('marks')->marks_from_user->__use($user) );
}

elseif (url_start_with('/tag/')) {
  redirect('/marks' . url());
}
elseif ($matches = url_match('/user/*')) {
  redirect(url() . '/marks');
}
else {
  unknown_url();
}

// });
