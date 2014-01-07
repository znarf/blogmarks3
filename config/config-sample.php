<?php

db::params(['host' => '127.0.0.1', 'name' => 'blogmarks', 'username' => 'root', 'password' => '']);

cache::$params = ['host' => 'localhost'];

service('search')->params([
  'host' => 'localhost', 'port' => '9200'
]);

service('redis')->params([
  'host' => 'localhost'
]);

service('amqp')->params([
  'host' => 'localhost', 'port' => '5672', 'username' => 'guest', 'password' => 'guest'
]);
