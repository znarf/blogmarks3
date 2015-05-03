<?php namespace blogmarks;

function config($name, $default = null, $value = null)
{
  if (!isset(blogmarks::$registry['config'])) {
    blogmarks::$registry['config'] = [];
  }
  if ($value) {
    return blogmarks::$registry['config'][$name] = $value;
  }
  else {
    return isset(blogmarks::$registry['config'][$name]) ? blogmarks::$registry['config'][$name] : $default;
  }
}
