<?php namespace blogmarks;

function section($value = null)
{
  return blogmarks::config('section', 'public', $value);
}
