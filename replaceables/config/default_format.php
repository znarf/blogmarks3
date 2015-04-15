<?php namespace blogmarks;

function default_format($value = null)
{
  return blogmarks::config('default_format', 'html', $value);
}
