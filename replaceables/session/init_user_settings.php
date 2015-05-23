<?php namespace blogmarks;

function init_user_settings($user)
{
  if (is_string($user->timezone) && strpos($user->timezone, '/')) {
    date_default_timezone_set($user->timezone);
  }
  # Transition from previous values
  if (is_numeric($user->lang)) {
    if ($user->lang == 1) {
      $user->lang = 'fr_FR';
    }
    if ($user->lang == 2) {
      $user->lang = 'en_US';
    }
  }
  if (is_string($user->lang) && strpos($user->lang, '_')) {
    putenv("LC_ALL={$user->lang}.utf8");
    setlocale(LC_ALL, "{$user->lang}.utf8");
  }
}
