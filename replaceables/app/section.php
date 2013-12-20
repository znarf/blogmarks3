<?php

return function($value = null) {
  static $section;
  return $value ? $section = $value : ($section ? $section : 'public');
};
