<?php

$target = anonymous_class();

$target->user = function($slug = null) {
  static $user;
  if ($slug) {
    $user = table('users')->get_one('login', urldecode($slug));
    if (!$user) throw http_error(404, 'User not found');
  }
  return $user;
};

$target->tag = function($slug = null) {
  static $tag;
  if ($slug) {
    $tag = table('tags')->get_one('label', urldecode($slug));
    if (!$tag) throw http_error(404, 'Tag not found');
  }
  return $tag;
};

$target->mark = function($slug = null) {
  static $mark;
  if ($slug) {
    $mark = table('marks')->get_one('id', urldecode($slug));
    if (!$mark) throw http_error(404, 'Mark not found');
  }
  return $mark;
};

return $target;
