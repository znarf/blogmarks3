<?php namespace blogmarks;

function generate_token($key)
{
  return $_SESSION["csrf_{$key}"] = blogmarks::generate_phrase();
}
