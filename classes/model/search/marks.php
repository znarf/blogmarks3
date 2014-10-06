<?php namespace blogmarks\model\search;

use
DateTime,
Exception;

class marks
{

  use
  \blogmarks\magic\registry;

  protected $documents = [];

  function to_array($mark)
  {
    return [
      'id'           => (int)$mark->id,
      'created_at'   => date(datetime::RFC3339, strtotime($mark->published)),
      'updated_at'   => date(datetime::RFC3339, strtotime($mark->updated)),
      'user_id'      => $mark->user_id,
      'link_id'      => $mark->link_id,
      'url'          => $mark->url,
      'title'        => $mark->title,
      'content'      => utf8_encode($mark->text),
      'public'       => $mark->is_public,
      'private'      => $mark->is_private,
      'tags'         => array_values(array_map('strval', $mark->public_tags())),
      'private_tags' => array_values(array_map('strval', $mark->private_tags()))
    ];
  }

  function available()
  {
    return $this->service('search')->client();
  }

  function asynchronous($async = true)
  {
    return $async && $this->service('amqp')->channel();
  }

  function index($mark, $async = true)
  {
    if ($this->asynchronous($async)) {
      $this->service('amqp')->push(['action' => 'index', 'mark_id' => $mark->id], 'marks-index');
    }
    else {
      $this->documents[] = new \elastica\document($mark->id, $this->to_array($mark));
      if (count($this->documents) >= 100) {
        $this->flush_index_buffer();
      }
    }
  }

  function flush_index_buffer()
  {
    if (count($this->documents)) {
      if ($client = $this->service('search')->client()) {
        try {
          $client->getindex('bm')->gettype('marks')->adddocuments($this->documents);
        }
        catch (exception $e) {
          error_log($e->getMessage());
          # error_log(json_encode($this->documents));
        }
      }
      $this->documents = [];
    }
  }

  function unindex($mark, $async = true)
  {
    if ($this->asynchronous($async)) {
      $this->service('amqp')->push(['action' => 'unindex', 'mark_id' => $mark->id], 'marks-index');
    }
    else {
      $this->service('search')->delete('/bm/marks/' . $mark->id);
    }
  }

  function index_user($user, $async = true)
  {
    if ($this->asynchronous($async)) {
      $this->service('amqp')->push(['action' => 'index_user', 'user_id' => $user->id], 'marks-index');
    }
    else {
      $marks = $this->model('marks')->private_marks_from_user($user, ['limit' => -1]);
      foreach ($marks['items'] as $mark) {
        $this->index($mark, false);
      }
    }
  }

  function unindex_user($user, $async = true)
  {
    if ($this->asynchronous($async)) {
      $this->service('amqp')->push(['action' => 'unindex_user', 'user_id' => $user->id], 'marks-index');
    }
    else {
      $this->service('search')->delete('/bm/marks/_query?q=user_id:' . $user->id);
    }
  }

  function build_base_query($params)
  {
    $query = [];

    if (empty($params['query'])) {
      $query['query']['filtered']['query'] = ['match_all' => []];
    }
    else {
      $query['query']['filtered']['query'] = ['multi_match' => [
        'query'  => $params['query'],
        'fields' => ['title^2', 'url', 'content', 'tags.partial']
      ]];
      if (!empty($params['private'])) {
        $query['query']['filtered']['query']['multi_match']['fields'][] = 'private_tags.partial';
      }
    }

    if (isset($params['user'])) {
      $user = $params['user'];
      $query['query']['filtered']['filter']['and'][] = ['term' => ['user_id' => $user->id]];
    }
    if (isset($params['tag'])) {
      $tag = $params['tag'];
      $query['query']['filtered']['filter']['and'][] = ['term' => ['tags' => (string)$tag]];
    }
    if (isset($params['tags'])) {
      foreach ($params['tags'] as $tag) {
        $query['query']['filtered']['filter']['and'][] = ['term' => ['tags' => (string)$tag]];
      }
    }
    if (empty($params['private'])) {
      $query['query']['filtered']['filter']['and'][] = ['term' => ['private' => false]];
    }

    if (isset($params['user_ids'])) {
      $or = [];
      foreach ($params['user_ids'] as $user_id) {
        $or[] = ['term' => ['user_id' => $user_id]];
      }
      $query['query']['filtered']['filter']['and'][] = ['or' => $or];
    }

    return $query;
  }

  function build_full_query($params, $query = [])
  {
    $order = $params['order'] == 'asc' ? 'asc' : 'desc';

    $query['sort'] = ['created_at' => ['order' => $order]];

    if (isset($params['before'])) {
      $before = date(datetime::RFC3339, $params['before'] - 1);
      $query['query']['filtered']['filter']['and'][] = ['range' => ['created_at' => ['to' => $before]]];
    }
    if (isset($params['after'])) {
      $after = date(datetime::RFC3339, $params['after']);
      $query['query']['filtered']['filter']['and'][] = ['range' => ['created_at' => ['from' => $after]]];
    }

    if (isset($params['limit'])) {
      $query['size'] = $params['limit'] + 1;
      if (isset($params['offset'])) {
        $query['from'] = $params['offset'];
      }
    }

    return $query;
  }

  function search($params = [])
  {
    if (!$this->available()) {
      throw new \amateur\core\exception('Search backend not available.', 500);
    }
    # Count Query
    $query = $this->build_base_query($params);
    $result = $this->service('search')->count('/bm/marks', $query);
    $total = (int) $result['count'];
    # Main Query
    $query = $this->build_full_query($params, $query);
    $result = $this->service('search')->search('/bm/marks', $query);
    # Next?
    if (count($result['hits']['hits']) > $params['limit']) {
      $hit = array_pop($result['hits']['hits']);
      $next = strtotime($hit['_source']['created_at']);
    }
    # Ids
    $ids = array_map(function($hit) { return (int) $hit['_id']; }, $result['hits']['hits']);
    # Items
    $items = $this->table('marks')->get($ids);
    # Results
    return compact('params', 'total', 'next', 'items');
  }

}
