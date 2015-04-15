<?php namespace blogmarks;

function brand($value = null)
{
  return blogmarks::config('brand', 'Blogmarks', $value);
}
