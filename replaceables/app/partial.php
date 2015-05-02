<?php namespace blogmarks;

function partial($name, $args = [])
{
  # Init Registry
  if (!isset(blogmarks::$registry['partials'])) {
    blogmarks::$registry['partials'] = [];
  }
  # Store Partial (callable expected)
  if (!empty($args) && is_callable($args)) {
    return blogmarks::$registry['partials'][$name] = $args;
  }
  # Stored Partial
  if (isset(blogmarks::$registry['partials'][$name])) {
    $partial = blogmarks::$registry['partials'][$name];
    return $partial($args);
  }
  # Default Partial
  $default_partial = blogmarks::replaceable('default_partial');
  $result = $default_partial($name, $args);
  # If a callable is returned
  if (is_callable($result)) {
    # Store It
    $partial = blogmarks::$registry['partials'][$name] = $result;
    # Execute it immediately
    $result = $partial($args);
  }
  # Return result
  return $result;
}
