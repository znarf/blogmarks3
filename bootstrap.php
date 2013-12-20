<?php

define('root_dir', __DIR__);

require root_dir . '/vendor/autoload.php';

define('amateur_dir', root_dir . '/vendor/amateur');

require_once amateur_dir . '/amateur.php';

app_dir(root_dir . '/application');

load_replaceables(root_dir . '/replaceables');

register_namespace('blogmarks', root_dir . '/classes');

include root_dir . '/config/config.php';
