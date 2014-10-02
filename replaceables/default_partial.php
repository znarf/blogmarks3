<?php

return function($name, $args = []) {
  if ($filename = filename('partial', $name)) {
    extract($args);
    include $filename;
    return;
  }
  throw http_error(500, "Unknown partial ($name).");
};
