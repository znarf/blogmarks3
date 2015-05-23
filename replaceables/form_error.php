<?php namespace blogmarks;

function form_error($name = null, $message = null)
{
  # Init Registry
  if (!isset(blogmarks::$registry['errors'])) {
    blogmarks::$registry['errors'] = [];
  }
  if ($name && $message) {
    return blogmarks::$registry['errors'][$name] = $message;
  }
  elseif ($name) {
    return isset(blogmarks::$registry['errors'][$name]) ? blogmarks::$registry['errors'][$name] : [];
  }
  return blogmarks::$registry['errors'];
};
