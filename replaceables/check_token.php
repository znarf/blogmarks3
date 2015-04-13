<?php namespace blogmarks;

function check_token($key, $token)
{
  if (empty($_SESSION["csrf_{$key}"])) {
    throw blogmarks::http_error(400, 'Missing session token.');
  }
  if (empty($token)) {
    throw blogmarks::http_error(400, 'Missing form token.');
  }
  if ($_SESSION["csrf_{$key}"] != $token) {
    throw blogmarks::http_error(400, 'Invalid token.');
  }
};
