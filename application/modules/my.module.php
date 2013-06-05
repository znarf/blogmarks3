<?php

domain('my');

section('my');

title('My Marks');

check_authenticated();
$user = authenticated_user();

if (url_is('/my/marks')) {
  tags_title('My', 'Tags');
  helper('container')->tags( model('tags')->__closure('from_user', $user, ['private' => 1]) );
  $app->marks( helper('feed')->__closure('private_marks_from_user', $user) );
}

elseif ($matches = url_match('/my/marks/tag/*')) {
  $tag = helper('target')->tag($matches[1]);
  title('My Marks', 'with tag ' . strong($tag->label));
  tags_title('My', 'Tags related with ' . strong($tag->label));
  helper('container')->tags( model('tags')->__closure('from_user_related_with', $user, $tag, ['private' => 1]) );
  $app->marks( helper('feed')->__closure('private_marks_from_user_with_tag', $user, $tag) );
}

elseif (url_is('/my/tags')) {
  $params = ['private' => true, 'limit' => get_int('limit', 100), 'search' => get_param('search', '')];
  ok(view('partials/taglist', ['tags' => model('tags')->from_user($user, $params)]));
}

elseif (url_is('/my/tags/autocomplete')) {
  $params = ['private' => true, 'limit' => get_int('limit', 10), 'search' => get_param('search', '')];
  $tags = [];
  foreach (model('tags')->from_user($user, $params) as $tag) $tags[] = (string)$tag;
  ok(json_encode($tags));
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
