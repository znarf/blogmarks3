<?php

# Action (will be re-integrated in amateur core later)

function action($name, $args = [])
{
  global $app;
  extract($args);
  include $app->dir() . '/actions/' . $name . '.action.php';
}

helper(['blogmarks', 'session', 'view', 'url']);

start_session();

if (url_is('/')) {
  redirect('/marks');
}
elseif (url_start_with('/my/tools')) {
  module('tools');
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
