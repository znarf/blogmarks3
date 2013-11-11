<?php namespace blogmarks\helper;

use
datetime,
markdownify,
amateur\http\request,
amateur\services\amqp;

use
Elastica\Client   as client,
Elastica\Document as document;

class search
{

  use \closurable_methods;

  static function params()
  {
    return [
      'offset' => get_int('offset', 0),
      'limit'  => get_int('limit', 10),
      'after'  => get_param('after'),
      'before' => get_param('before'),
      'order'  => get_param('order', 'desc')
    ];
  }

  static function to_array($mark)
  {
    if ($mark->contentType == 'html') {
      require_once root_dir . '/lib/markdownify/markdownify.php';
      $md = new markdownify;
      $content = $md->parseString($mark->content);
    } else {
      $content = $mark->content;
    }
    return [
      'id'          => (int)$mark->id,
      'created_at'  => date(datetime::RFC3339, strtotime($mark->published)),
      'updated_at'  => date(datetime::RFC3339, strtotime($mark->updated)),
      'user_id'     => $mark->user_id,
      'link_id'     => $mark->link_id,
      'url'         => $mark->url,
      'title'       => $mark->title,
      'content'     => $content,
      'public'      => $mark->is_public,
      'private'     => $mark->is_private,
      'tags'        => array_map('strval', $mark->tags)
    ];
  }

  static function index_url()
  {
    global $es_params;
    return 'http://' . $es_params['host'] . ':' . $es_params['port'] . '/bm';
  }

  static $connection;

  static function connection()
  {
    if (self::$connection) {
      return self::$connection;
    }
    global $es_params;
    $client = new client($es_params);
    return self::$connection = $client->getIndex('bm')->getType('marks');
  }

  static $documents = [];

  static function index($mark, $async = true)
  {
    if ($async) {
      amqp::push(['action' => 'index', 'mark_id' => $mark->id], 'marks-index');
    }
    else {
      self::$documents[] = new document($mark->id, self::to_array($mark));
      if (count(self::$documents) > 1000) {
        self::flush();
      }
      /*
      $response = (new request)->put_json(self::index_url() . '/marks/' . $mark->id, self::to_array($mark));
      */
    }
  }

  static function flush()
  {
    if (count(self::$documents)) {
      self::connection()->addDocuments(self::$documents);
      self::$documents = [];
    }
  }

  static function unindex($mark, $async = true)
  {
    if ($async) {
      amqp::push(['action' => 'unindex', 'mark_id' => $mark->id], 'marks-index');
    }
    else {
      $response = (new request)->delete(self::index_url() . '/marks/' . $mark->id);
    }
  }

  static function index_user($user, $async = true)
  {
    if ($async) {
      amqp::push(['action' => 'index_user', 'user_id' => $user->id], 'marks-index');
    }
    else {
      set_param('limit', -1);
      $marks = helper('marks')->private_marks_from_user($user);
      foreach ($marks['items'] as $mark) self::index($mark, false);
    }
  }

  static function unindex_user($user, $async = true)
  {
    if ($async) {
      amqp::push(['action' => 'unindex_user', 'user_id' => $user->id], 'marks-index');
    }
    else {
      $response = (new request)->delete(self::index_url() . '/marks/_query?q=user_id:' . $user->id);
    }
  }

  static function search($options = [])
  {
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

    $result = (new request)->post_json(self::index_url() . '/marks/_search', $query);

    $result = json_decode($result->body, true);

    $total = $result['hits']['total'];

    $ids = array_map(function($hit) { return $hit['_id']; }, $result['hits']['hits']);

    $items = model('marks')->get($ids);

    return compact('params', 'total', 'items');
  }

}

return new search;

# return replaceable('search', instance('\Blogmarks\Helper\Search'));
