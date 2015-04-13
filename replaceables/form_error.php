<?php namespace blogmarks;

function form_error($name = null, $message = null)
{
  # Init Registry
  if (!isset(blogmarks::$registry['brand'])) {
    blogmarks::$registry['brand'] = [];
  }
  if ($name && $message) {
    return $errors[$name] = $message;
  }
  elseif ($name) {
    return isset($errors[$name]) ? $errors[$name] : [];
  }
  return $errors;
};
