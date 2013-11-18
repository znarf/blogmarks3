<?php namespace blogmarks\helper;

class container
{

  static $tags;

  function tags($value = null)
  {
    if ($value) {
      return static::$tags = $value;
    }
    $tags = static::$tags;
    return is_callable($tags) ? $tags() : $tags;
  }

  static $marks;

  function marks($value = null)
  {
    if ($value) {
      return static::$marks = $value;
    }
    $marks = static::$marks;
    return is_callable($marks) ? $marks() : $marks;
  }

}

return new container;
