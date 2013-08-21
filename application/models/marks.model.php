<?php

use \Amateur\Model\Table as Table;
use \Amateur\Model\Ressource as Ressource;
use \Amateur\Model\Dynamize as Dynamize;

class Marks extends Table
{

  use Closurize;

  public $classname = 'Mark';
  public $tablename = 'bm_marks';
  public $primary = 'id';
  public $unique_indexes = ['id'];
  public $collection_indexes = ['author'];

  function ids_and_ts($where = [])
  {
    $query = "SELECT id, UNIX_TIMESTAMP(published) as ts" .
             " FROM bm_marks" .
             " WHERE " . db_build_where($where);
    $result = db_query($query);
    return db_fetch_all($result);
  }

  function latest_ids_and_ts($params = [])
  {
    $query = "SELECT id, UNIX_TIMESTAMP(published) as ts" .
             " FROM bm_marks" .
             " WHERE visibility = 0 AND display = 1" .
             " ORDER BY published DESC" .
             " LIMIT 1000";
    $result = db_query($query);
    return db_fetch_all($result);
  }

  function ids_and_ts_with_tag($tag, $params = [])
  {
    $query = "SELECT m.id as id, UNIX_TIMESTAMP(m.published) as ts" .
             " FROM bm_marks as m, bm_marks_has_bm_tags as mht" .
             " WHERE m.id = mht.mark_id AND mht.tag_id = {$tag->id}" .
             " AND m.visibility = 0";
    $result = db_query($query);
    return db_fetch_all($result);
  }

  function ids_and_ts_from_user_with_tag($user, $tag, $params = [])
  {
    $query = "SELECT m.id as id, UNIX_TIMESTAMP(m.published) as ts" .
             " FROM bm_marks as m, bm_marks_has_bm_tags as mht" .
             " WHERE m.author = {$user->id}" .
             " AND m.id = mht.mark_id AND mht.tag_id = {$tag->id}";
    if (isset($params['visibility'])) {
      $query .= " AND m.visibility = " . $params['visibility'];
    }
    $result = db_query($query);
    return db_fetch_all($result);
  }

  function delete($mark)
  {
    return parent::delete($mark);
  }

  function create($params = [])
  {
    $params += ['contentType' => 'text'];
    $params += ['published' => db_now(), 'updated' => db_now()];
    return parent::create($params);
  }

  function update($mark, $params = [])
  {
    $params += ['updated' => db_now()];
    return parent::update($mark, $params);
  }

  function with_ids($ids)
  {
    model(['marks-tags', 'tags', 'screenshots']);
    $keys = [];
    foreach ($ids as $id) array_push($keys, "bm_marks_raw_id_$id", "bm_marks_tags_id_$id");
    cache_preload($keys);
    return parent::with_ids($ids);
  }

}

class Mark extends Ressource
{

  use Dynamize;

  function is_public()
  {
    return $this->visibility == 0;
  }

  function is_private()
  {
    return $this->visibility == 1;
  }

  function classname($user = null)
  {
    $classname = $this->visibility == 1 ? 'mark private' : 'mark';
    if (is_object($user) && $user->id == $this->attribute('author')) {
      $classname .= ' own';
    }
    return $classname;
  }

  function author()
  {
    return model('users')->get($this->attribute('author'));
  }

  function related()
  {
    return model('links')->get($this->attribute('related'));
  }

  function tags($tags = null)
  {
    return isset($tags) ? model('marks-tags')->tag_mark($this, $tags) : model('marks-tags')->from_mark($this);
  }

  function screenshot()
  {
    if (!$screenshot = $this->attribute('screenshot')) {
      $result = model('screenshots')->search([
        'where' => ['link' => $this->attribute('related'), 'status' => 1],
        'order' => 'created DESC'
      ]);
      if ($row = db_fetch_assoc($result)) {
        $screenshot = $row['url'];
      }
      if (empty($screenshot)) {
        $pu = parse_url($this->url);
        $screenshot = 'http://open.thumbshots.org/image.pxf?url=' . $pu['host'];
      }
      self::cache('screenshot', $screenshot);
    }
    return $screenshot;
  }

  function url()
  {
    if (!$url = $this->attribute('url')) {
      $url = $this->related->href;
      self::cache('url', $url);
    }
    return $url;
  }

  function cache($key, $value)
  {
    $cache_key = model('marks')->tablename . "_raw_id_{$this->id}";
    if ($row = cache_get($cache_key)) {
      $row[$key] = $value;
      cache_set($cache_key, $row);
    }
  }

}

return instance('Marks');
