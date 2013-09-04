<?php

use \Amateur\Model\Cache as Cache;
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
    return $this
      ->select('id, UNIX_TIMESTAMP(published) as ts')
      ->where($where)
      ->fetch_all();
  }

  function latest_ids_and_ts()
  {
    return $this
      ->select('id, UNIX_TIMESTAMP(published) as ts')
      ->where(['visibility' => 0, 'display' => 1])
      ->order_by('published DESC')
      ->limit(1000)
      ->fetch_all();
  }

  function ids_and_ts_with_tag($tag)
  {
    return $this
      ->select('m.id, UNIX_TIMESTAMP(m.published) as ts')
      ->from('bm_marks as m, bm_marks_has_bm_tags as mht')
      ->where('m.id = mht.mark_id')
      ->and_where(['mht.tag_id' => $tag->id, 'm.visibility' => 0])
      ->fetch_all();
  }

  function ids_and_ts_from_user_with_tag($user, $tag, $params = [])
  {
    $query = $this
      ->select('m.id, UNIX_TIMESTAMP(m.published) as ts')
      ->from('bm_marks as m, bm_marks_has_bm_tags as mht')
      ->where('m.id = mht.mark_id')
      ->and_where(['m.author' => $user->id, 'mht.tag_id' => $tag->id]);
    if (isset($params['visibility'])) {
      $query->and_where(['m.visibility' => $params['visibility']]);
    }
    return $query->fetch_all();
  }

  function delete($mark)
  {
    return parent::delete($mark);
  }

  function create($params = [])
  {
    $params += ['contentType' => 'text'];
    $params += ['published' => Db::now(), 'updated' => Db::now()];
    return parent::create($params);
  }

  function update($mark, $params = [])
  {
    $params += ['updated' => Db::now()];
    return parent::update($mark, $params);
  }

  function get($arg)
  {
    if (!is_array($arg)) {
      return $this->get_one('id', $arg);
    }
    # Ids
    $ids = array_map('intval', $arg);
    # Preload cache keys
    $cache_keys = [];
    foreach ($ids as $id) array_push($cache_keys, "bm_marks_raw_id_$id", "bm_marks_tags_id_$id");
    Cache::preload($cache_keys);
    # Get marks objects
    $marks = $this->get_all('id', $ids);
    # Load tags
    model('marks-tags')->load_from_ids($ids);
    # Load links
    model('links')->load_from_marks($marks);
    # Load screenshots
    model('screenshots')->load_from_marks($marks);
    # Preload user cache keys
    $user_keys = array_map(function($mark) { return "bm_users_raw_id_" . $mark->attribute('author'); }, $marks);
    Cache::preload($user_keys);
    # Return
    return $marks;
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
      $query = model('screenshots')
        ->select('url')
        ->where(['link' => $this->attribute('related'), 'status' => 1])
        ->order_by('created DESC');
      $row = $query->fetch_one();
      $screenshot = $row ? $row['url'] : $this->default_screenshot();
      self::cache_attribute('screenshot', $screenshot);
    }
    return $screenshot;
  }

  function default_screenshot()
  {
    $pu = parse_url($this->url);
    return 'http://open.thumbshots.org/image.pxf?url=' . $pu['host'];
  }

  function url()
  {
    if (!$url = $this->attribute('url')) {
      $url = $this->related->href;
      self::cache_attribute('url', $url);
    }
    return $url;
  }

  function cache_attribute($key, $value)
  {
    $this->_attributes[$key] = $value;
    $cache_key = model('marks')->cache_key('id', $this->id);
    if ($row = Cache::get($cache_key)) {
      $row[$key] = $value;
      Cache::set($cache_key, $row);
    }
  }

}

return new Marks;
