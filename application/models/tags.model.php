<?php

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
    return ($tag = self::get_one('label', $label)) ? $tag : self::create(['label' => $label]);
  }

  function related_with($tag)
  {
    $query = "SELECT mht2.tag_id as id,  mht2.label, COUNT(*) as count" .
             " FROM bm_marks_has_bm_tags as mht1, bm_marks_has_bm_tags as mht2" .
             " WHERE mht1.tag_id = " . db_quote($tag->id) .
             " AND mht2.mark_id = mht1.mark_id" .
             " AND mht2.isHidden = 0 " .
             (flag('db_old_schema') ? "" : " AND mht2.visibility = 0") .
             " AND mht2.tag_id != " . db_quote($tag->id) .
             " GROUP BY mht2.tag_id ORDER BY count DESC LIMIT 50";
    $result = db_query($query);
    return db_fetch_objects($result, 'Tag');
  }

  function fetch_from_user($user, $status)
  {
    $query = "SELECT label, COUNT(*) as count FROM bm_marks_has_bm_tags" .
             " WHERE user_id = " . db_quote($user->id) .
             ($status == 'public' ? " AND isHidden = 0" : "") .
             " GROUP BY tag_id";
    $result = db_query($query);
    return db_fetch_all($result);
  }

  function from_user($user, $params = [])
  {
    $params = $params + ['private' => false, 'limit' => 100];
    // New Redis Code
    $status = $params['private'] ? 'private' : 'public';
    return $this->tag_cloud("tag_cloud_user_{$user->id}_{$status}", $params, $this->fetch_from_user->__use($user, $status));
    // Old Direct Code
    /*
    $query = "SELECT tag_id as id, label, COUNT(*) as count" .
             " FROM bm_marks_has_bm_tags" .
             " WHERE user_id = " . db_quote($user->id);
    if ($params['private'] == false) {
        $query .= ' AND isHidden = 0';
        if (!flag('db_old_schema')) $query .= ' AND visibility = 0';
    }
    if (!empty($params['search'])) {
      $query .= " AND label LIKE " . db_quote('%' . $params['search'] . '%');
    }
    $query .= " GROUP BY tag_id ORDER BY count DESC LIMIT " . (int)$params['limit'];
    $result = db_query($query);
    return db_fetch_objects($result, 'Tag');
    */
  }


  function search_from_user($user, $params = [])
  {
    $params = $params + ['private' => false, 'limit' => 100];
    $status = $params['private'] ? 'private' : 'public';
    $tags = $this->tag_cloud("tag_cloud_user_{$user->id}_{$status}", ['limit' => null], $this->fetch_from_user->__use($user, $status));
    foreach ($tags as $index => $tag) {
      if (!empty($params['search']) && strpos($tag->label, $params['search']) === false) {
        unset($tags[$index]);
      }
    }
    return array_slice($tags, $params['offset'], $params['limit']);
  }

  function tag_cloud($redis_key, $params, $callback)
  {
    global $redis;
    $tags = [];
    $params = $params + ['offset' => 0, 'limit' => 100];
    if (!$redis->exists($redis_key)) {
      $results = $callback();
      foreach ($results as $result) $redis->zAdd($redis_key, $result['count'], $result['label']);
    }
    $options = ['withscores' => true, 'limit' => [$params['offset'], $params['limit']]];
    $results = $redis->zRevRangeByScore($redis_key, '+inf', 1, $options);
    foreach ($results as $label => $count) {
      if (empty($params['search']) || strpos($label, $params['search']) !== false) {
        $tags[] = new Tag(['label' => $label, 'count' => $count]);
      }
    }
    return $tags;
  }

  function from_user_related_with($user, $tag, $params = [])
  {
    $params = $params + ['private' => false, 'limit' => 50];
    $query = "SELECT mht2.tag_id as id,  mht2.label, COUNT(*) as count" .
             " FROM bm_marks_has_bm_tags as mht1, bm_marks_has_bm_tags as mht2" .
             " WHERE mht1.user_id = " . db_quote($user->id) .
             " AND mht1.tag_id = " . db_quote($tag->id) .
             " AND mht2.mark_id = mht1.mark_id " .
             ($params['private'] == true ? '' : " AND mht2.isHidden = 0") .
             " AND mht2.tag_id != " . db_quote($tag->id) .
             " GROUP BY mht2.tag_id ORDER BY count DESC LIMIT " . (int)$params['limit'];
    $result = db_query($query);
    return db_fetch_objects($result, 'Tag');
  }

  function latests()
  {
    $now = date('Y-m-d H:i:s');
    $query = "SELECT mht.tag_id as id, mht.label, COUNT(*) as count" .
             " FROM bm_marks as m, bm_marks_has_bm_tags as mht" .
             " WHERE m.published > DATE_SUB('$now', INTERVAL 1 YEAR)" .
             " AND m.visibility = 0" .
             " AND mht.mark_id = m.id AND mht.isHidden = 0" .
             " GROUP BY mht.tag_id ORDER BY count DESC LIMIT 50";
    $result = db_query($query);
    return db_fetch_objects($result, 'Tag');
  }

}

class Tag extends Ressource
{

  function __toString()
  {
    return $this->label;
  }

}

return instance('Tags');
