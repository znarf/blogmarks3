<?php namespace blogmarks\model\search;

class marks
{

  use
  \blogmarks\magic\registry;

  protected $documents = [];

  function to_array($mark)
  {
    return [
      'id'          => (int)$mark->id,
      'created_at'  => date(\DateTime::RFC3339, strtotime($mark->published)),
      'updated_at'  => date(\DateTime::RFC3339, strtotime($mark->updated)),
      'user_id'     => $mark->user_id,
      'link_id'     => $mark->link_id,
      'url'         => $mark->url,
      'title'       => $mark->title,
      'content'     => utf8_encode($mark->text),
      'public'      => $mark->is_public,
      'private'     => $mark->is_private,
      'tags'        => array_map('strval', $mark->tags)
    ];
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
      $this->documents[] = new \Elastica\Document($mark->id, $this->to_array($mark));
      if (count($this->documents) > 1000) {
        $this->flush();
      }
    }
  }

  function flush()
  {
    if (count($this->documents)) {
      if ($client = $this->service('search')->client()) {
        $client->getIndex('bm')->getType('marks')->addDocuments($this->documents);
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

  function build_query($params)
  {
    $query = [];

    $query['query']['filtered']['query'] = ['match_all' => []];

    $order = $params['order'] == 'asc' ? 'asc' : 'desc';

    $query['sort'] = ['created_at' => ['order' => $order]];

    if (isset($params['user'])) {
      $user = $params['user'];
      $query['query']['filtered']['filter']['and'][] = ['term' => ['user_id' => $user->id]];
    }
    if (isset($params['tag'])) {
      $tag = $params['tag'];
      $query['query']['filtered']['filter']['and'][] = ['term' => ['tags' => $tag->label]];
    }
    if (isset($params['tags'])) {
      foreach ($params['tags'] as $tag) {
        $query['query']['filtered']['filter']['and'][] = ['term' => ['tags' => $tag->label]];
      }
    }
    if (empty($params['private'])) {
      $query['query']['filtered']['filter']['and'][] = ['term' => ['private' => false]];
    }

    if (isset($params['limit'])) {
      $query['size'] = $params['limit'];
      if (isset($params['offset'])) {
        $query['from'] = $params['offset'];
      }
    }

    if (isset($params['before'])) {
      $before = date(\DateTime::RFC3339, $params['before'] - 1);
      $query['query']['filtered']['filter']['and'][] = ['range' => ['created_at' => ['to' => $before]]];
    }
    if (isset($params['after'])) {
      $after = date(\DateTime::RFC3339, $params['after']);
      $query['query']['filtered']['filter']['and'][] = ['range' => ['created_at' => ['from' => $after]]];
    }

    return $query;
  }

  function search($params = [])
  {
    $query = $this->build_query($params);
    $result = $this->service('search')->search('/bm/marks', $query);
    $total = $result['hits']['total'];
    $ids = array_map(function($hit) { return $hit['_id']; }, $result['hits']['hits']);
    $items = $this->table('marks')->get($ids);
    return compact('params', 'total', 'items');
  }

}
