<?php

\amateur\model\db::params(['host' => '127.0.0.1', 'name' => 'blogmarks', 'username' => 'root', 'password' => '']);

\amateur\model\cache::params(['host' => 'localhost']);

service('search')->params([
  'host' => 'localhost', 'port' => '9200'
]);

service('redis')->params([
  'host' => 'localhost'
]);

service('amqp')->params([
  'host' => 'localhost', 'port' => '5672', 'username' => 'guest', 'password' => 'guest'
]);

// Secret used to sign "remember me" cookies, e.g. bin2hex(random_bytes(32))
// flag('secret', '');
