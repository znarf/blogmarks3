<?php namespace blogmarks\service;

use
amateur\http\request;

class search
{

  protected $params;

  function params($params = null)
  {
    return $params ? $this->params = $params : $this->params;
  }

  protected $client;

  function client($client = null)
  {
    if ($client) {
      $this->client = $client;
    }
    if ($this->client) {
      return $this->client;
    }
    if (!$this->params) {
      return;
    }
    $client = new \Elastica\Client($this->params);
    return $this->client = $client;
  }

  function base_url()
  {
    return 'http://' . $this->params['host'] . ':' . $this->params['port'];
  }

  function delete($url)
  {
    if ($this->client()) {
      (new request)->delete($this->base_url() . $url);
    }
  }

  function search($url, $query = [])
  {
    $response = (new request)->post_json($this->base_url() . $url . '/_search', $query);
    $result = json_decode($response->body, true);
    return $result;
  }

}
