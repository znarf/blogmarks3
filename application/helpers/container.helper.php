<?php

$container = anonymous_class();

$container->tags = function($value = null) {
  static $tags;
  if ($value) {
    return $tags = $value;
  }
  return is_callable($tags) ? $tags() : $tags;
};

$container->marks = function($value = null) {
  static $marks;
  if ($value) {
    return $marks = $value;
  }
  return is_callable($marks) ? $marks() : $marks;
};

return $container;
