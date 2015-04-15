<?php namespace blogmarks;

function domain($value = null)
{
  return blogmarks::config('domain', 'public', $value);
}
