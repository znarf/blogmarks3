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
             " WHERE " . db_build_where($where + ['contentType' => 'html']);
    $result = db_query($query);
    return db_fetch_all($result);
  }

  function latest_ids_and_ts($params = [])
  {
    $query = "SELECT id, UNIX_TIMESTAMP(published) as ts" .
             " FROM bm_marks" .
             " WHERE visibility = 0" .
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

  function classname()
  {
     return $this->visibility == 1 ? 'mark private' : 'mark';
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
    if ($screenshot = $this->related->screenshot) {
      return $screenshot->url;
    }
    $pu = parse_url($this->url);
    if (!empty($pu['host'])) {
      return 'http://open.thumbshots.org/image.pxf?url=' . $pu['host'];
    }
  }

  function url()
  {
    return $this->related->href;
  }

}

return function() { static $instance; return $instance ? $instance : $instance = new Marks; };
