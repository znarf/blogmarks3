#!/usr/bin/env php
<?php

use
amateur\model\cache,
amateur\model\table;

require_once dirname(__DIR__) . '/bootstrap.php';

service('amqp')->consume('marks-index', function($message, $ack, $nack) {
  $search = search('marks');
  if ($message['action'] == 'index') {
    $mark = table('marks')->get($message['mark_id']);
    if ($mark) $search->index($mark, false);
  }
  elseif ($message['action'] == 'unindex') {
    $mark = table('marks')->get($message['mark_id']);
    if ($mark) $search->unindex($mark, false);
  }
  elseif ($message['action'] == 'index_user') {
    $user = table('users')->get($message['user_id']);
    if ($user) $search->index_user($user, false);
  }
  elseif ($message['action'] == 'unindex_user') {
    $user = table('users')->get($message['user_id']);
    if ($user) $search->unindex_user($user, false);
  }
  $ack();
  cache::store();
  cache::flush();
  table::flush();
  if ($message['action'] == 'exit') {
    $search->flush();
    exit;
  }
});
