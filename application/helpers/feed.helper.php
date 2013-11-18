<?php namespace blogmarks\helper;

use
amateur\model\redis;

class feed
{

  use \closurable_methods;

  static function params()
  {
    return [
      'offset' => get_int('offset', 0),
      'limit'  => get_int('limit', 10),
      'order'  => get_param('order', 'desc'),
      'after'  => get_param('after', '-inf'),
      'before' => get_param('before', '+inf')
    ];
  }

  static function marks($redis_key, $query)
  {
    $redis = redis::connection();
    $params = self::params(); /* ['offset' => 0, 'limit' => 10, 'after' => '-inf', 'before' => '+inf']; */
    # Without Redis
    if (!$redis->exists($redis_key)) {
      # Fetch Query
      $order = $params['order'] == 'asc' ? 'published ASC' : 'published DESC';
      $results = $query()->order_by($order)->fetch_key_values('id', 'ts');
      # Delayed Storage
      register_shutdown_function(function() use($redis_key, $results) {
        $redis = redis::connection();
        foreach ($results as $id => $ts) $redis->zAdd($redis_key, $ts, $id);
      });
      # Soft offset/limit
      $total = count($results);
      $ids = array_keys($results);
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
    $items = empty($ids) ? [] : model('marks')->get($ids);
    return compact('params', 'total', 'items');
  }

  static function add($redis_key, $ts, $id)
  {
    $redis = redis::connection();
    if ($redis->exists($redis_key)) $redis->zAdd($redis_key, $ts, $id);
  }

  static function remove($redis_key, $id)
  {
    $redis = redis::connection();
    if ($redis->exists($redis_key)) $redis->zRem($redis_key, $id);
  }

  static function flush($redis_key)
  {
    $redis = redis::connection();
    $redis->delete($redis_key);
  }

  static function index($mark)
  {
    $ts = strtotime($mark->published);
    # Global Feed
    if ($mark->is_public()) self::add("feed_marks", $ts, $mark->id);
    # Update User Feeds
    self::add("feed_marks_my_{$mark->author->id}", $ts, $mark->id);
    if ($mark->is_public()) self::add("feed_marks_user_{$mark->author->id}", $ts, $mark->id);
    # Update Tag Feeds
    foreach ($mark->tags() as $mt) {
      if ($mark->is_public()) self::add("feed_marks_tag_{$mt->tag_id}", $ts, $mark->id);
      self::add("feed_marks_my_{$mark->author->id}_tag_{$mt->tag_id}", $ts, $mark->id);
    }
  }

  static function unindex($mark)
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
  }

}

return new feed;
