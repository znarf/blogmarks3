<?php

define('root_dir', __DIR__);

define('amateur_dir', root_dir . '/vendor/amateur');

# Composer
require root_dir . '/vendor/autoload.php';

# Classes (would not be necessary if amateur was loaded with composer)
require_once amateur_dir . '/classes/core/loader.php';
\amateur\core\loader::register_namespace('amateur', amateur_dir . '/classes');

# Classes
\amateur\core\loader::register_namespace('blogmarks', root_dir . '/classes');

# Replaceables
if (false && file_exists(root_dir . '/replaceables.php')) {
  require root_dir . '/replaceables.php';
}
else {
  \amateur\core\replaceable::instance()->load(amateur_dir . '/replaceables');
  \amateur\core\replaceable::instance()->load(root_dir . '/replaceables');
}

# Locale
textdomain('blogmarks');
bindtextdomain('blogmarks', root_dir . '/locale');

# Application
app_dir(root_dir . '/application');

require root_dir . '/config/config.php';
