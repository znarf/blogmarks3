<?php

require dirname(__DIR__) . '/../bootstrap.php';

$app->default_format = 'rss';

$app->path('/api');

$app->start();

$app->end();
