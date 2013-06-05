<?php

define('root_dir', dirname(dirname(__DIR__)));

define('app_dir', root_dir . '/application');

define('amateur_dir', root_dir . '/vendor/amateur');

require_once amateur_dir . '/core/core.functions.php';
require_once amateur_dir . '/core/closure.functions.php';
require_once amateur_dir . '/core/replaceable.functions.php';

require_once app_dir . '/classes/app.class.php';
$app = core('app', new \Blogmarks\App);

require_once amateur_dir . '/amateur.dsl.php';

include root_dir . '/config.php';

# Set Default Format for API
$app->default_format = 'rss';

# Remove /api from URL
# url(preg_replace('/' . '\/api' . '/', '', url()));

$app->path('/api');

start(app_dir);
