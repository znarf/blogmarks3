<?php namespace Blogmarks\Helper;

class Feed
{

  use \Closurize;

  static function redis_connection()
  {
    global $redis;
    if (empty($redis)) {
      $redis = new Redis;
      $redis->connect('localhost');
    }
    return $redis;
  }

  static function params()
  {
    return [
      'offset' => get_int('offset', 0),
      'limit'  => get_int('limit', 10),
      'after'  => get_param('after', '-inf'),
      'before' => get_param('before', '+inf')
    ];
  }

  static function marks($redis_key = null, $callback = null)
  {
    // error_log("redis:marks:$redis_key");
    $redis = self::redis_connection();
    $params = self::params() + ['offset' => 0, 'limit' => 10, 'after' => '-inf', 'before' => '+inf'];
    if (!$redis->exists($redis_key)) {
      $results = $callback();
      foreach ($results as $result) $redis->zAdd($redis_key, $result['ts'], $result['id']);
    }
    $options = ['withscores' => false, 'limit' => [$params['offset'], $params['limit']]];
    $total = $redis->zCard($redis_key);
    // error_log("redis:total:$total");
    if ($total == 0) {
      $items = [];
    } else {
      // error_log(json_encode($params));
      // error_log(json_encode($options));
      $ids = $redis->zRevRangeByScore($redis_key, "(" . $params['before'], "(" . $params['after'], $options);
      $items = model('marks')->with_ids($ids);
    }
    // error_log("redis:items:" . count($items));
    return compact('params', 'total', 'items');
  }

  static function add($redis_key, $ts, $id)
  {
    error_log("redis:add:$redis_key:$id");
    $redis = self::redis_connection();
    if ($redis->exists($redis_key)) $redis->zAdd($redis_key, $ts, $id);
  }

  static function remove($redis_key, $id)
  {
    error_log("redis:remove:$redis_key:$id");
    $redis = self::redis_connection();
    if ($redis->exists($redis_key)) $redis->zRem($redis_key, $id);
  }

  static function flush($redis_key)
  {
    $redis = self::redis_connection();
    $redis->delete($redis_key);
  }

  static function index($mark)
  {
    $ts = strtotime($mark->published);
    # Global Feed
    self::add("feed_marks", $ts, $mark->id);
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

  # Model Helpers

  static function latest_marks()
  {
    return self::marks(
      "feed_marks",
      model('marks')->latest_ids_and_ts
    );
  }

  static function marks_with_tag($tag)
  {
    return self::marks(
      "feed_marks_tag_{$tag->id}",
      model('marks')->ids_and_ts_with_tag->__use($tag)
    );
  }

  static function public_marks_from_user($user)
  {
    return self::marks(
      "feed_marks_user_{$user->id}",
      model('marks')->ids_and_ts->__use(['author' => $user->id, 'visibility' => 0])
    );
  }

  static function private_marks_from_user($user)
  {
    return self::marks(
      "feed_marks_my_{$user->id}",
      model('marks')->ids_and_ts->__use(['author' => $user->id])
    );
  }

  static function private_marks_from_user_with_tag($user, $tag)
  {
    return self::marks(
      "feed_marks_my_{$user->id}_tag_{$tag->id}",
      model('marks')->ids_and_ts_from_user_with_tag->__use($user, $tag)
    );
  }

}

return replaceable('feed', single('Blogmarks\Helper\Feed'));
