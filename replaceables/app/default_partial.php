<?php namespace blogmarks;

function default_partial($name, $args = [])
{
  if ($filename = blogmarks::filename('partial', $name)) {
    extract($args);
    $result = include $filename;
    return $result;
  }
  throw blogmarks::http_error(500, "Unknown partial ($name).");
};
