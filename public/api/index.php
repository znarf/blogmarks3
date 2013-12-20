<?php

require dirname(__DIR__) . '/../bootstrap.php';

default_format('rss');

app_path('/api');

start();

finish();
