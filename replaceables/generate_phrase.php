<?php namespace blogmarks;

function generate_phrase($length = 64)
{
  $chars = '1234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $i = 0;
  $phrase = '';
  while ($i < $length) {
    $phrase .= $chars[mt_rand(0, strlen($chars) - 1)];
    $i++;
  }
  return $phrase;
}
