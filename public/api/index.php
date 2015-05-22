<?php

require dirname(__DIR__) . '/../bootstrap.php';

default_format('atom');

app_path('/api');

run();
