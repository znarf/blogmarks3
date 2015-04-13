<?php namespace blogmarks;

function brand($value = null)
{
  # Init Registry
  if (!isset(blogmarks::$registry['brand'])) {
    blogmarks::$registry['brand'] = 'Blogmarks';
  }
  # Get / Set
  return $value ? blogmarks::$registry['brand'] = $value : blogmarks::$registry['brand'];
}
