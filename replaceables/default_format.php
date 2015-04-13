<?php namespace blogmarks;

function default_format($value = null)
{
  # Init Registry
  if (!isset(blogmarks::$registry['default_format'])) {
    blogmarks::$registry['default_format'] = 'html';
  }
  # Get / Set
  return $value ? blogmarks::$registry['default_format'] = $value : blogmarks::$registry['default_format'];
}
