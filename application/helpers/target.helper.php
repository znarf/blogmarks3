<?php namespace Blogmarks\Helper;

class Target
{

  static $user;

  function user($slug = null)
  {
    if ($slug) {
      static::$user = model('users')->get_one('login', urldecode($slug));
      if (!static::$user) throw http_error(404, 'User not found');
    }
    return static::$user;
  }

  static $tag;

  function tag($slug = null)
  {
    if ($slug) {
      static::$tag = model('tags')->get_one('label', urldecode($slug));
      if (!static::$tag) throw http_error(404, 'Tag not found');
    }
    return static::$tag;
  }

  static $mark;

  function mark($slug = null)
  {
    if ($slug) {
      static::$mark = model('marks')->get_one('id', urldecode($slug));
      if (!static::$mark) throw http_error(404, 'Mark not found');
    }
    return static::$mark;
  }

}

$target = new Target;

replaceable('target', function($name = null, $slug = null) use($target) {
  return $name ? $target->$name($slug) : $target;
});

return $target;
