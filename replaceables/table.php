<?php namespace blogmarks;

function table($name)
{
  # Multi
  if ($name === (array)$name) {
    return array_map(__function__, $name);
  }
  return registry::table($name);
}
