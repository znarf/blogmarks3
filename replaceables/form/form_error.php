<?php

return function($name = null, $message = null) {
  static $errors = [];
  if ($name && $message) {
    return $errors[$name] = $message;
  }
  elseif ($name) {
    return isset($errors[$name]) ? $errors[$name] : [];
  }
  return $errors;
};
