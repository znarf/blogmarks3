<?php

require dirname(__DIR__) . '/../bootstrap.php';

default_format('rss');

$app->path('/api');

$app->start();

$app->end();
