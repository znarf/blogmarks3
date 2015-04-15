<?php namespace blogmarks;

function side_title($base = null, $arg = null)
{
  # Default
  if (!isset(blogmarks::$registry['side_title'])) {
    blogmarks::$registry['side_title'] = '<strong>Public</strong> Tags';
  }
  # Set
  if ($base) {
    blogmarks::$registry['side_title'] = blogmarks::strong($base);
    if ($arg) {
      blogmarks::$registry['side_title'] .= ' ' . $arg;
    }
  }
  # Return
  return blogmarks::$registry['side_title'];
}
