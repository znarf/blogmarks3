<?php

return function($user) {
  if (is_string($user->timezone) && strpos($user->timezone, '/')) {
    date_default_timezone_set($user->timezone);
  }
  if (is_numeric($user->lang)) {
    if ($user->lang == 2) {
      $user->lang = 'fr_FR';
    }
  }
  if (is_string($user->lang) && strpos($user->lang, '_')) {
    putenv("LC_ALL={$user->lang}");
    setlocale(LC_ALL, $user->lang);
  }
};
