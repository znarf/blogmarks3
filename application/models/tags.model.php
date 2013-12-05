<?php namespace blogmarks\model;

use
amateur\model\db,
amateur\model\cache,
amateur\model\redis,
amateur\model\table,
amateur\model\ressource;

class tags extends table
{

  use \closurable_methods;

  public $namespace = __namespace__;

  public $classname = 'tag';

  public $tablename = 'bm_tags';

  public $unique_indexes = ['id', 'label'];

  function with_label($label)
  {
    $tag = self::get_one('label', $label);
    return $tag ? $tag : self::create(['label' => $label]);
  }

  # Shortcuts

  function latests($params = [])
  {
    return $this->search($params);
  }

  function related_with($tag, $params = [])
  {
    return $this->search(['tag' => $tag] + $params);
  }

  function from_user($user, $params = [])
  {
    return $this->search(['user' => $user] + $params);
  }

  function from_user_related_with($user, $tag, $params = [])
  {
    return $this->search(['user' => $user, 'tag' => $tag] + $params);
  }

  function private_from_user($user, $params = [])
  {
    return $this->search(['user' => $user, 'private' => true] + $params);
  }

  function private_from_user_related_with($user, $tag, $params = [])
  {
    return $this->search(['user' => $user, 'tag' => $tag, 'private' => true] + $params);
  }

  # Generic

  function tag_key($params = [])
  {
    $cache_key = "tags";
    if (isset($params['user'])) {
      $user = $params['user'];
      $cache_key .= "_user_{$user->id}";
    }
    if (isset($params['tag'])) {
      $tag = $params['tag'];
      $cache_key .= "_tag_{$tag->id}";
    }
    $cache_key .= $params['private'] ? '_private' : '_public';
    return $cache_key;
  }

  function tag_query($params = [])
  {
    if (isset($params['user'], $params['tag'])) {
      return $this->query_from_user_related_with($params['user'], $params['tag'], $params['private']);
    }
    elseif (isset($params['user'])) {
      return $this->query_from_user($params['user'], $params['private']);
    }
    elseif (isset($params['tag'])) {
      return $this->query_related_with($params['tag']);
    }
    else {
      return $this->query_latests();
    }
  }

  function tag_cloud($redis_key, $params)
  {
    $redis = redis::connection();
    $params = $params + ['offset' => 0, 'limit' => 50];
    # Without Redis
    if (!$redis->exists($redis_key)) {
      # Fetch Query
      $query = $this->tag_query($params)->order_by('count DESC, label DESC');
      $results = $query->fetch_key_values('label', 'count');
      # Delayed Storage
      register_shutdown_function(function() use($redis_key, $results) {
        $redis = redis::connection();
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
        $tags[] = new Tag(['label' => $label, 'count' => $count]);
      }
    }
    return $tags;
  }

  function search($params = [])
  {
    $params = $params + ['private' => false];
    $cache_key = $this->tag_key($params);
    if (empty($params['query'])) {
      return $this->tag_cloud($cache_key, $params);
    }
    else {
      $tags = $this->tag_cloud($cache_key, ['limit' => null] + $params);
      return array_slice($tags, 0, $params['limit']);
    }
  }

  # Query

  function query_latests()
  {
    $query = $this
      ->select('mht.id, mht.label as label, COUNT(*) as count')
      ->from('bm_marks as m, bm_marks_has_bm_tags as mht')
      ->where('mht.mark_id = m.id')
      ->and_where("m.published > DATE_SUB(NOW(), INTERVAL 1 YEAR)")
      ->and_where(['m.visibility' => 0, 'mht.isHidden' => 0])
      ->group_by('mht.tag_id')
      ->limit(1000);
    return $query;
  }

  function query_related_with($tag, $private = false)
  {
    $query = $this
      ->select('mht2.tag_id as id, mht2.label, COUNT(*) as count')
      ->from('bm_marks_has_bm_tags as mht1, bm_marks_has_bm_tags as mht2')
      ->where('mht2.mark_id = mht1.mark_id')
      ->and_where(['mht1.tag_id' => $tag->id])
      ->and_where('mht2.tag_id != ' . db::quote($tag->id))
      ->group_by('mht2.tag_id');
    if (!$private) {
      $query->and_where(['mht2.isHidden' => 0]);
      if (!flag('db_old_schema')) {
        $query->and_where(['mht2.visibility' => 0]);
      }
    }
    return $query;
  }

  function query_from_user($user, $private = false)
  {
    $query = $this
      ->select('id, label, COUNT(*) as count')
      ->from('bm_marks_has_bm_tags')
      ->where(['user_id' => $user->id])
      ->group_by('tag_id');
    if (!$private) {
      $query->and_where(['isHidden' => 0]);
      if (!flag('db_old_schema')) {
        $query->and_where(['visibility' => 0]);
      }
    }
    return $query;
  }

  function query_from_user_related_with($user, $tag, $private = false)
  {
   return $this->query_related_with($tag, $private)->and_where(['mht1.user_id' => $user->id]);
  }

  # Ratios

  function private_ratios_for_user($user)
  {
    # From Cache
    $cache_key = "bm_marks_has_bm_tags_private_ratios_{$user->id}";
    $objects = cache::get($cache_key);
    if (is_array($objects)) {
      return $objects;
    }
    # From DB
    $ratios = $this->table('marks_tags')
      ->select('(SUM(isHidden) / COUNT(*) * 100) AS ratio, label')
      ->where(['user_id' => $user->id])
      ->group_by('label')
      ->having('ratio > 0')
      ->fetch_key_values('label', 'ratio');
    # Set Cache
    cache::set($cache_key, $ratios);
    # Return
    return $ratios;
  }

}

class tag extends ressource
{

  function __toString()
  {
    return (string)$this->label;
  }

}

return model('tags', tags::instance());
