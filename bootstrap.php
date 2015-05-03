<?php namespace amateur;

define('root_dir', __dir__);

# Composer
require root_dir . '/vendor/autoload.php';

# Blogmarks Replaceables
replaceable::load(root_dir . '/replaceables' , 'blogmarks');

# Expose global replaceables
replaceable::expose_global_functions();

# Locale
textdomain('blogmarks');
bindtextdomain('blogmarks', root_dir . '/locale');

# Application
app_dir(root_dir . '/application');

# Config
require root_dir . '/config/config.php';
