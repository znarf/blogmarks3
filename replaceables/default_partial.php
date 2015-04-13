<?php namespace blogmarks;

function default_partial($name, $args = [])
{
  if ($filename = blogmarks::filename('partial', $name)) {
    extract($args);
    include $filename;
    return;
  }
  throw blogmarks::http_error(500, "Unknown partial ($name).");
};
