<?php

return function($name, $value = null) {
  # Set
  if ($value) {
    # TODO
  }
  # Multi
  if ($name === (array)$name) {
    return array_map('model', $name);
  }
  # Model
  if ($name == 'marks' || $name == 'tags') {
    return \blogmarks\registry::model($name);
  }
  # Table
  return table($name);
};
