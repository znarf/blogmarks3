<?php

# Action (will be re-integrated in amateur core later)

function action($name, $args = [])
{
  global $app;
  extract($args);
  include $app->dir() . '/actions/' . $name . '.action.php';
}

# Flag (should be integrated in amateur core later)

function flag($name, $value = null)
{
  if ($value) if (!defined($name)) define($name, $value);
  if (defined($name)) return constant($name);
}

/*
flag('db_read_only', true);

flag('db_old_schema', true);
*/

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
