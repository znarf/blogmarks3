<?php

list($target, $container) = helper(['target', 'container']);

title('Public Marks');

if (url_is('/marks')) {
  tags_title('Public', 'Tags');
  $container->tags( model('tags')->latests );
  $app->marks( helper('feed')->latest_marks );
}

elseif ($matches = url_match('/marks/tag/*')) {
  $tag = $target->tag($matches[1]);
  title('Public Marks', 'with tag ' . strong($tag->label));
  tags_title('Tags', 'related with ' . strong($tag->label));
  $container->tags( model('tags')->related_with->__use($tag) );
  $app->marks( helper('feed')->marks_with_tag->__use($tag) );
}

elseif ($matches = url_match('/user/*/marks')) {
  $user = $target->user($matches[1]);
  title('Public Marks', 'from ' . strong($user->name));
  tags_title('Tags', 'from ' . strong($user->name));
  $container->tags( model('tags')->from_user->__use($user) );
  $app->marks( helper('feed')->public_marks_from_user->__use($user) );
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
