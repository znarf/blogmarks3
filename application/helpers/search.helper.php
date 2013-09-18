<?php namespace blogmarks\helper;

use
datetime,
amateur\http\request;

class Search
{

  use \closurable_methods;

  static function params()
  {
    return [
      'offset' => get_int('offset', 0),
      'limit'  => get_int('limit', 10),
      'after'  => get_param('after'),
      'before' => get_param('before')
    ];
  }

  static function search($options = [])
  {
    global $es_params;
    $index_url = 'http://' . $es_params['host'] . ':' . $es_params['port'] . '/bm';

    $params = self::params();

    $query = [];
    $query['query']['filtered']['query'] = ['match_all' => []];
    $query['sort'] = ['created_at' => ['order' => 'desc']];

    if (isset($options['user'])) {
      $user = $options['user'];
      $query['query']['filtered']['filter']['and'][] = ['term' => ['user_id' => $user->id]];
    }
    if (isset($options['tag'])) {
      $tag = $options['tag'];
      $query['query']['filtered']['filter']['and'][] = ['term' => ['tags' => $tag->label]];
    }
    if (isset($options['tags'])) {
      foreach ($options['tags'] as $tag)
        $query['query']['filtered']['filter']['and'][] = ['term' => ['tags' => $tag->label]];
    }
    if (empty($options['private'])) {
      $query['query']['filtered']['filter']['and'][] = ['term' => ['private' => false]];
    }

    if (isset($params['limit'])) {
      $query['size'] = $params['limit'];
      if (isset($params['offset'])) {
        $query['from'] = $params['offset'];
      }
    }
    if (isset($params['before'])) {
      $before = date(datetime::RFC3339, $params['before'] - 1);
      $query['query']['filtered']['filter']['and'][] = ['range' => ['created_at' => ['to' => $before]]];
    }
    if (isset($params['after'])) {
      $after = date(datetime::RFC3339, $params['after']);
      $query['query']['filtered']['filter']['and'][] = ['range' => ['created_at' => ['from' => $after]]];
    }

    $result = request::create()->post_json($index_url . '/marks/_search', $query);

    $result = json_decode($result->body, true);

    $total = $result['hits']['total'];

    $ids = array_map(function($hit) { return $hit['_id']; }, $result['hits']['hits']);

    $items = model('marks')->get($ids);

    return compact('params', 'total', 'items');
  }

}

return new Search;

# return replaceable('search', instance('\Blogmarks\Helper\Search'));
