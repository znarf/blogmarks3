<?php namespace blogmarks;

function model($name, $value = null)
{
  # Multi
  if ($name === (array)$name) {
    return array_map(__function__, $name);
  }
  # Model
  if ($name == 'marks' || $name == 'tags') {
    return registry::model($name);
  }
  # Table
  return blogmarks::table($name);
}
