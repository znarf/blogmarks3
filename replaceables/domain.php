<?php namespace blogmarks;

function domain($value = null)
{
  # Init Registry
  if (!isset(blogmarks::$registry['domain'])) {
    blogmarks::$registry['domain'] = 'public';
  }
  # Get / Set
  return $value ? blogmarks::$registry['domain'] = $value : blogmarks::$registry['domain'];
};
