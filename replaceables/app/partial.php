<?php

return function($name, $args = []) {
  static $partials = [];
  # Stored Partial
  if (isset($partials[$name])) {
    $partial = $partials[$name];
    return $partial($args);
  }
  # Load Partial
  extract($args);
  $result = include filename('partial', $name);
  # Closure Partial
  if ($result && is_callable($result)) {
    $partials[$name] = $result;
    return $result($args);
  }
};
