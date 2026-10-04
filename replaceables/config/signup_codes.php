<?php namespace blogmarks;

# Invitation codes required to sign up, one per line in config/codes.txt
function signup_codes()
{
  $file = root_dir . '/config/codes.txt';
  if (!file_exists($file)) {
    return [];
  }
  return array_values(array_filter(array_map('trim', file($file))));
}
