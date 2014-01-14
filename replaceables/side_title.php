<?php

return function($base = null, $arg = null) {
  static $side_title;
  if ($base) {
    $side_title = strong($base);
    if ($arg) {
      $side_title .= ' ' . $arg;
    }
  }
  return $side_title ? $side_title : '<strong>Public</strong> Tags';
};
