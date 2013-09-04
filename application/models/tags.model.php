<?php

use \Amateur\Model\Db as Db;
use \Amateur\Model\Cache as Cache;
use \Amateur\Model\Table as Table;
use \Amateur\Model\Ressource as Ressource;

class Tags extends Table
{

  use Closurize;

  public $classname = 'Tag';
  public $tablename = 'bm_tags';
  public $primary = 'id';
  public $unique_indexes = ['id', 'label'];

  function with_label($label)
  {
    $tag = self::get_one('label', $label);
    return $tag ? $tag : self::create(['label' => $label]);
  }

  function tag_cloud($redis_key, $params, $callback)
  {
    global $redis;
    $params = $params + ['offset' => 0, 'limit' => 100];
    if (!$redis->exists($redis_key)) {
      $results = $callback();
      foreach ($results as $result) $redis->zAdd($redis_key, $result['count'], $result['label']);
    }
    $options = ['withscores' => true, 'limit' => [$params['offset'], $params['limit']]];
    $results = $redis->zRevRangeByScore($redis_key, '+inf', 1, $options);
    # Return as tags
    $tags = [];
    foreach ($results as $label => $count) {
      if (empty($params['search']) || strpos($label, $params['search']) !== false) {
        $tags[] = new Tag(['label' => $label, 'count' => $count]);
      }
    }
    return $tags;
  }

  # User

  function from_user($user, $params = [])
  {
    $params = $params + ['private' => false, 'limit' => 100];
    $status = $params['private'] ? 'private' : 'public';
    $callback = model('marks-tags')->fetch_from_user->__use($user, $status);
    if (empty($params['search'])) {
      return $this->tag_cloud("tag_cloud_user_{$user->id}_{$status}", $params, $callback);
    }
    else {
      $tags = $this->tag_cloud("tag_cloud_user_{$user->id}_{$status}", ['limit' => null] + $params, $callback);
      return array_slice($tags, 0, $params['limit']);
    }
  }

  # Related

  function related_query($tag, $params = [])
  {
    $params = $params + ['private' => false, 'limit' => 50];
    $query = $this->query()
      ->select('mht2.tag_id as id, mht2.label, COUNT(*) as count')
      ->from('bm_marks_has_bm_tags as mht1, bm_marks_has_bm_tags as mht2')
      ->where('mht2.mark_id = mht1.mark_id')
      ->and_where(['mht1.tag_id' => $tag->id])
      ->and_where('mht2.tag_id != ' . Db::quote($tag->id))
      ->group_by('mht2.tag_id')
      ->order_by('count DESC');
    if (!$params['private']) {
      $query->and_where(['mht2.isHidden' => 0]);
      if (!flag('db_old_schema')) {
        $query->and_where(['mht2.visibility' => 0]);
      }
    }
    return $query;
  }

  function related_with($tag, $params = [])
  {
    $query = $this->related_query($tag, $params);
    return $query->fetch_objects('Tag');
  }

  function from_user_related_with($user, $tag, $params = [])
  {
    $query = $this->related_query($tag, $params);
    $query->and_where(['mht1.user_id' => $user->id]);
    return $query->fetch_objects('Tag');
  }

  # Latests

  function fetch_latests()
  {
    $now = date('Y-m-d H:i:s');
    $query = $this->query()
      ->select('mht.label as label, COUNT(*) as count')
      ->from('bm_marks as m, bm_marks_has_bm_tags as mht')
      ->where('mht.mark_id = m.id')
      ->and_where("m.published > DATE_SUB('$now', INTERVAL 1 YEAR)")
      ->and_where(['m.visibility' => 0, 'mht.isHidden' => 0])
      ->group_by('mht.tag_id')
      ->order_by('count DESC')
      ->limit(1000);
    return $query->fetch_all();
  }

  function latests($params = [])
  {
    $params = $params + ['limit' => 50];
    return $this->tag_cloud("tag_cloud_public", $params, $this->fetch_latests);
  }

  # Ratios

  function private_ratios_for_user($user)
  {
    # From Cache
    $cache_key = "bm_marks_has_bm_tags_private_ratios_{$user->id}";
    $objects = Cache::get($cache_key);
    if (is_array($objects)) {
      return $objects;
    }
    # From DB
    $query = $this->query()
      ->select('(SUM(isHidden) / COUNT(*) * 100) AS ratio, label')
      ->from('bm_marks_has_bm_tags')
      ->where(['user_id' => $user->id])
      ->group_by('label')
      ->having('ratio > 0');
    $result = $query->execute();
    $ratios = [];
    while ($row = Db::fetch_assoc($result)) {
      $label = $row['label'];
      $ratios[$label] = (int)$row['ratio'];
    }
    Cache::set($cache_key, $ratios);
    return $ratios;
  }

}

class Tag extends Ressource
{

  function __toString()
  {
    return $this->label;
  }

}

return new Tags;
