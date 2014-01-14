<?php

return function($base = null, $arg = null) {
  static $title;
  if ($base) {
    $title = $base;
    if ($arg) {
      $title .= ' <span class="arg">' . $arg . '</span>';
    }
  }
  return $title;
};
