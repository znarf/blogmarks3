<?php namespace blogmarks\model\feed;

use
amateur\model\query;

class marks
{

  use
  \blogmarks\magic\registry;

  static $default_params = [
    'offset' => 0,
    'limit'  => 10,
    'order'  => 'desc',
    'after'  => '-inf',
    'before' => '+inf',
  ];

  function redis()
  {
    return $this->service('redis')->connection();
  }

  function query($redis_key, $query, $params = [])
  {
    $redis = self::redis();
    # Params
    $params = array_filter($params) + self::$default_params;
    # Without Redis
    if (!$redis || !$redis_key || !$redis->exists($redis_key)) {
      # Fetch Query
      if (!$query instanceof query && is_callable($query)) {
        $query = $query();
      }
      $order = $params['order'] == 'asc' ? 'published ASC' : 'published DESC';
      $results = $query->order_by($order)->fetch_key_values('id', 'ts');
      # Delayed Storage
      if ($redis && $redis_key) register_shutdown_function(function() use($redis, $redis_key, $results) {
        foreach ($results as $id => $ts) $redis->zAdd($redis_key, $ts, $id);
      });
      # Get total
      $total = count($results);
      # Soft after/before
      if (($before = $params['before']) != '+inf') {
        $results = array_filter($results, function($ts) use($before) { return $ts < $before; });
      }
      if (($after = $params['after']) != '-inf') {
        $results = array_filter($results, function($ts) use($after) { return $ts > $after; });
      }
      # Get Ids
      $ids = array_keys($results);
      # Soft offset/limit
      if ($params['limit']) {
        $ids = array_slice($ids, $params['offset'], $params['limit']);
      }
    }
    # With Redis
    else {
      $options = ['withscores' => false, 'limit' => [$params['offset'], $params['limit']]];
      if ($total = $redis->zCard($redis_key)) {
        if ($params['order'] == 'asc') {
          $ids = $redis->zRangeByScore($redis_key, "(" . $params['after'], "(" . $params['before'], $options);
        }
        else {
          $ids = $redis->zRevRangeByScore($redis_key, "(" . $params['before'], "(" . $params['after'], $options);
        }
      }
    }
    $items = empty($ids) ? [] : $this->table('marks')->get($ids);
    return compact('params', 'total', 'items');
  }

  function add($redis_key, $ts, $id)
  {
    $redis = self::redis();
    if ($redis && $redis->exists($redis_key)) $redis->zAdd($redis_key, $ts, $id);
  }

  function remove($redis_key, $id)
  {
    $redis = self::redis();
    if ($redis && $redis->exists($redis_key)) $redis->zRem($redis_key, $id);
  }

  function flush($redis_key)
  {
    $redis = self::redis();
    if ($redis && $redis->exists($redis_key)) $redis->delete($redis_key);
  }

  function index($mark)
  {
    $ts = strtotime($mark->published);
    # Global Feed
    if ($mark->is_public) self::add("feed_marks", $ts, $mark->id);
    # Update User Feeds
    self::add("feed_marks_my_{$mark->author->id}", $ts, $mark->id);
    if ($mark->is_public) self::add("feed_marks_user_{$mark->author->id}", $ts, $mark->id);
    # Update Tag Feeds
    foreach ($mark->tags() as $mt) {
      if ($mark->is_public && !$mt->isHidden) self::add("feed_marks_tag_{$mt->tag_id}", $ts, $mark->id);
      self::add("feed_marks_my_{$mark->author->id}_tag_{$mt->tag_id}", $ts, $mark->id);
    }
    # Update Friends Feeds
    if ($mark->is_public) {
      foreach ($mark->user->follower_ids as $user_id) {
        self::add("feed_marks_friends_{$user_id}}", $ts, $mark->id);
      }
    }
  }

  function unindex($mark)
  {
    # Global Feed
    self::remove("feed_marks", $mark->id);
    # Update User Feeds
    self::remove("feed_marks_my_{$mark->author->id}", $mark->id);
    self::remove("feed_marks_user_{$mark->author->id}", $mark->id);
    # Update Tag Feeds
    foreach ($mark->tags as $mt) {
      self::remove("feed_marks_tag_{$mt->tag_id}", $mark->id);
      self::remove("feed_marks_my_{$mark->author->id}_tag_{$mt->tag_id}", $mark->id);
    }
    # Update Friends Feeds
    foreach ($mark->user->follower_ids as $user_id) {
      self::add("feed_marks_friends_{$user_id}}", $ts, $mark->id);
    }
  }

}
