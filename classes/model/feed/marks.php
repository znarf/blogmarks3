<?php namespace blogmarks\model\feed;

use
amateur\model\query;

class marks
{

  use
  \blogmarks\magic\registry;

  static $default_params = [
    'offset' => 0,
    'limit'  => 25,
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
    $results = self::ids_and_ts($redis_key, $query, $params);
    # Return prepared items
    return self::prepare_items($results, $params);
  }

  function ids_and_ts($redis_key, $query, $params = [])
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
      # Soft after/before
      if (($before = $params['before']) != '+inf') {
        $results = array_filter($results, function($ts) use($before) { return $ts < $before; });
      }
      if (($after = $params['after']) != '-inf') {
        $results = array_filter($results, function($ts) use($after) { return $ts > $after; });
      }
      # Soft offset/limit
      if ($params['limit'] > 0) {
        $results = array_slice($results, $params['offset'], $params['limit'] + 1, true);
      }
    }
    # With Redis
    else {
      $options = ['withscores' => true];
      if ($params['limit'] > 0) {
        $options['limit'] = [$params['offset'], $params['limit'] + 1];
      }
      if ($total = $redis->zCard($redis_key)) {
        if ($params['order'] == 'asc') {
          # error_log("redis:zRangeByScore:$redis_key");
          $results = $redis->zRangeByScore($redis_key, "" . $params['after'], "(" . $params['before'], $options);
        }
        else {
          # error_log("redis:zRevRangeByScore:$redis_key");
          $results = $redis->zRevRangeByScore($redis_key, "" . $params['before'], "(" . $params['after'], $options);
        }
      }
    }
    return $results;
  }

  function prepare_items($results, $params = [])
  {
    # Next?
    $next = $params['limit'] && count($results) > $params['limit'] ? (int) array_pop($results) : null;
    # Items
    $items = empty($results) ? [] : $this->table('marks')->get(array_keys($results));
    # Result
    return compact('params', 'total', 'next', 'items');
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
    $ts = $mark->published->getTimestamp();
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
      self::remove("feed_marks_friends_{$user_id}}", $mark->id);
    }
  }

}
