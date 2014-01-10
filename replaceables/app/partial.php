<?php

return function($name, $args = []) {
  # Registry
  static $partials = [];
  # Store partial (closure expected)
  if ($args !== (array)$args && is_callable($args)) {
    return $partials[$name] = $args;
  }
  # Stored Partial
  if (isset($partials[$name])) {
    $partial = $partials[$name];
    return $partial($args);
  }
  # Default Partial
  return default_partial($name, $args);
};
