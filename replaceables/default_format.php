<?php

return function($value = null) {
  static $default_format;
  return $value ? $default_format = $value : ($default_format ? $default_format : 'html');
};
