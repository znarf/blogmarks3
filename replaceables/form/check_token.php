<?php

return function($key, $token) {
  if (empty($_SESSION["csrf_{$key}"])) {
    throw http_error(400, 'Missing session token.');
  }
  if (empty($token)) {
    throw http_error(400, 'Missing form token.');
  }
  if ($_SESSION["csrf_{$key}"] != $token) {
    throw http_error(400, 'Invalid token.');
  }
};
