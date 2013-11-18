<?php

replaceable('container', function($name = null, $value = null) {
  $container = helper('container');
  return $name ? $container->$name($value) : $container;
});

replaceable('target', function($name = null, $slug = null) {
  $target = helper('target');
  return $name ? $target->$name($slug) : $target;
});

replaceable('render', function($name) {
  if ($name == 'marks') {
    helper('render')->marks();
  }
  else {
    response()->render($name);
  }
});
