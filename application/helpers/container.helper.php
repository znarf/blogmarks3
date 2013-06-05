<?php namespace Blogmarks\Helper;

class Container
{

  static $tags;

  function tags($value = null)
  {
    $tags = $value ? static::$tags = $value : static::$tags;
    return is_callable($tags) ? $tags() : $tags;
  }

  static $marks;

  function marks($value = null)
  {
    $marks = $value ? static::$marks = $value : static::$marks;
    return is_callable($marks) ? $marks() : $marks;
  }

}

$container = new Container;

replaceable('container', function($name = null, $value = null) use($container) {
  return $name ? $container->$name($value) : $container;
});

return $container;
