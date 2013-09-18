<?php

define('root_dir', __DIR__);

define('app_dir', root_dir . '/application');

require root_dir . '/vendor/autoload.php';

require_once root_dir . '/vendor/amateur/amateur.dsl.php';
require_once root_dir . '/vendor/amateur/extended.dsl.php';

require_once app_dir . '/app.dsl.php';

include root_dir . '/config.php';

$app->ns('blogmarks');

$app->dir(app_dir);
