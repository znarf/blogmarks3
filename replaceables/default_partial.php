<?php

return function($name, $args = []) {
  if ($filename = filename('partial', $name)) {
    extract($args);
    return include $filename;
  }
  throw http_error(500, "Unknown partial ($name).");
};
