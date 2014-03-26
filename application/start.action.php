<?php

start_session();

if ($user = authenticated_user()) {
  if (is_string($user->timezone) && strpos($user->timezone, '/')) {
    date_default_timezone_set($user->timezone);
  }
  if (is_string($user->lang) && strpos($user->lang, '_')) {
    putenv("LC_ALL={$user->lang}");
    setlocale(LC_ALL, $user->lang);
  }
}

if (url_is('/')) {
  redirect('/marks');
}
elseif (url_start_with('/my/tools')) {
  module('tools');
}
elseif (url_start_with('/my/profile')) {
  module('profile');
}
elseif (url_start_with('/my')) {
  module('my');
}
elseif (url_start_with('/auth')) {
  module('auth');
}
else {
  module('public');
}
