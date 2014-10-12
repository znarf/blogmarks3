<?php namespace blogmarks\model\feed;

use
amateur\model\query,
blogmarks\model\resource\tag;

class tags
{

  use
  \blogmarks\magic\registry;

  function redis()
  {
    return $this->service('redis')->connection();
  }

  function query($redis_key, $query, $params = [])
  {
    $redis = $this->redis();
    $params = $params + ['offset' => 0, 'limit' => 50];
    # Without Redis
    if (!$redis || !$redis_key || !$redis->exists($redis_key)) {
      # Fetch Query
      if (!$query instanceof query && is_callable($query)) {
        $query = $query();
      }
      $results = $query->order_by('count DESC')->fetch_key_values('label', 'count');
      # Delayed Storage
      if ($redis && $redis_key) register_shutdown_function(function() use($redis, $redis_key, $results) {
        foreach ($results as $label => $count) $redis->zAdd($redis_key, $count, $label);
      });
      # Soft offset/limit
      if ($params['limit']) {
        $results = array_slice($results, $params['offset'], $params['limit'], true);
      }
    }
    # With Redis
    else {
      # error_log("redis:zRevRangeByScore:$redis_key");
      $options = ['withscores' => true, 'limit' => [$params['offset'], $params['limit']]];
      $results = $redis->zRevRangeByScore($redis_key, '+inf', 1, $options);
    }
    # Return as tags (filtered with query if available)
    $tags = [];
    foreach ($results as $label => $count) {
      if (empty($params['query'])) {
        $tags[] = new tag(['label' => $label, 'count' => $count]);
      }
      else {
        foreach (explode(' ', $params['query']) as $token) {
          if (stripos($label, $token) !== false) {
            $tags[] = new tag(['label' => $label, 'count' => $count]);
          }
        }
      }
    }
    return $tags;
  }

}
