<?php

return function($name) {
  # Multi
  if ($name === (array)$name) {
    return array_map('table', $name);
  }
  return \blogmarks\registry::table($name);
};
