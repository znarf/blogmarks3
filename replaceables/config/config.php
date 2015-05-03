<?php namespace blogmarks;

function config($name, $default = null, $value = null)
{
  if (!isset(amateur::$registry['config'])) {
    amateur::$registry['config'] = [];
  }
  if ($value) {
    return amateur::$registry['config'][$name] = $value;
  }
  else {
    return isset(amateur::$registry['config'][$name]) ? amateur::$registry['config'][$name] : $default;
  }
}
