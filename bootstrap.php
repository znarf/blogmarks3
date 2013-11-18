<?php

define('root_dir', __DIR__);

define('app_dir', root_dir . '/application');

require root_dir . '/vendor/autoload.php';

require_once root_dir . '/vendor/amateur/autoload.php';
require_once root_dir . '/vendor/amateur/amateur.dsl.php';
require_once root_dir . '/vendor/amateur/extended.dsl.php';

$app = app();

$app->ns('blogmarks');

$app->dir(app_dir);

$app->load_functions();

$app->register_autoload();

include root_dir . '/config.php';
