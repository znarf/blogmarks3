<?php namespace blogmarks\model\feed;

use
blogmarks\model\ressource\tag;

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
    if (!$redis->exists($redis_key)) {
      # Fetch Query
      $results = $query->order_by('count DESC')->fetch_key_values('label', 'count');
      # Delayed Storage
      register_shutdown_function(function() use($redis, $redis_key, $results) {
        foreach ($results as $label => $count) $redis->zAdd($redis_key, $count, $label);
      });
      # Soft offset/limit
      if ($params['limit']) {
        $results = array_slice($results, $params['offset'], $params['limit'], true);
      }
    }
    # With Redis
    else {
      $options = ['withscores' => true, 'limit' => [$params['offset'], $params['limit']]];
      $results = $redis->zRevRangeByScore($redis_key, '+inf', 1, $options);
    }
    # Return as tags
    $tags = [];
    foreach ($results as $label => $count) {
      if (empty($params['query']) || stripos($label, $params['query']) !== false) {
        $tags[] = new tag(['label' => $label, 'count' => $count]);
      }
    }
    return $tags;
  }

}
