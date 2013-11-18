<?php

function generate_phrase($length = 64)
{
  $chars = '1234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $i = 0;
  $phrase = '';
  while ($i < $length) {
    $phrase .= $chars{mt_rand(0, strlen($chars)-1)};
    $i++;
  }
  return $phrase;
}

function generate_token($key)
{
  return $_SESSION["csrf_{$key}"] = generate_phrase();;
}

function check_token($key, $token)
{
  if (empty($_SESSION["csrf_{$key}"]))
    throw http_error(400, 'Missing session token.');
  if (empty($token))
    throw http_error(400, 'Missing form token.');
  if ($_SESSION["csrf_{$key}"] != $token)
    throw http_error(400, 'Invalid token.');
}
