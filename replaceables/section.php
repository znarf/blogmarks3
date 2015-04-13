<?php namespace blogmarks;

function section($value = null)
{
  # Init Registry
  if (!isset(blogmarks::$registry['section'])) {
    blogmarks::$registry['section'] = 'public';
  }
  # Get / Set
  return $value ? blogmarks::$registry['section'] = $value : blogmarks::$registry['section'];
}
