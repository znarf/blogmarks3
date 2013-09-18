<?php

defined('app_dir') || define('app_dir', __dir__);

require_once app_dir . '/classes/app.class.php';
$app = core('app', new \blogmarks\app);

function action($name, $args = [])
{
  extract($args);
  include core('app')->dir() . '/actions/' . $name . '.action.php';
}

function flag($name, $value = null)
{
  if ($value) if (!defined($name)) define($name, $value);
  if (defined($name)) return constant($name);
}
