<?php namespace blogmarks\service;

class redis
{

  protected $params;

  function params($params = null)
  {
    return $params ? $this->params = $params : $this->params;
  }

  protected $connection;

  function connection($connection = null)
  {
    if ($connection) {
      $this->connection = $connection;
    }
    if ($this->connection) {
      return $this->connection;
    }
    if (!class_exists('\redis', false) || !$this->params) {
      return;
    }
    $connection = new \redis;
    $connection->pconnect($this->params['host']);
    return $this->connection = $connection;
  }

}
