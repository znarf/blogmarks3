<?php namespace blogmarks\model;

use
amateur\model\db,
amateur\model\cache,
amateur\model\table,
amateur\model\ressource,
amateur\model\other_tables,
amateur\model\dynamic_properties;

class marks extends table
{

  use \closurable_methods;

  public $namespace = __namespace__;

  public $classname = 'mark';

  public $tablename = 'bm_marks';

  public $primary = 'id';

  public $unique_indexes = ['id'];

  public $collection_indexes = ['author'];

  function create($params = [])
  {
    $params += ['contentType' => 'text'];
    $params += ['published' => db::now(), 'updated' => db::now()];
    return parent::create($params);
  }

  function update($mark, $params = [])
  {
    $params += ['updated' => db::now()];
    return parent::update($mark, $params);
  }

  function delete($mark)
  {
    return parent::delete($mark);
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
    cache::preload($cache_keys);
    # Get marks objects
    $marks = $this->get_all('id', $ids);
    # Load marks tags
    $this->table('marks_tags')->load_from_ids($ids);
    # Load links
    $this->table('links')->load_from_marks($marks);
    # Load screenshots
    $this->table('screenshots')->load_from_marks($marks);
    # Preload user cache keys
    $user_keys = array_map(function($mark) { return "bm_users_raw_id_" . $mark->attributes['author']; }, $marks);
    cache::preload($user_keys);
    # Return
    return $marks;
  }

  # Queries

  function query_ids_and_ts($where = [])
  {
    return $this
      ->select('id, UNIX_TIMESTAMP(published) as ts')
      ->where($where);
  }

  function query_latest_ids_and_ts()
  {
    return $this
      ->query_ids_and_ts(['visibility' => 0, 'display' => 1])
      ->order_by('published DESC')
      ->limit(1000);
  }

  function query_ids_and_ts_from_user($user, $params = [])
  {
    $query = $this->query_ids_and_ts(['author' => $user->id]);
    if (empty($params['private'])) {
      $query->and_where(['visibility' => 0]);
    }
    return $query;
  }

  function query_ids_and_ts_with_tag($tag)
  {
    return $this
      ->select('m.id, UNIX_TIMESTAMP(m.published) as ts')
      ->from('bm_marks as m, bm_marks_has_bm_tags as mht')
      ->where('m.id = mht.mark_id')
      ->and_where(['mht.tag_id' => $tag->id, 'm.visibility' => 0, 'm.display' => 1]);
  }

  function query_ids_and_ts_from_user_with_tag($user, $tag, $params = [])
  {
    $query = $this
      ->select('m.id, UNIX_TIMESTAMP(m.published) as ts')
      ->from('bm_marks as m, bm_marks_has_bm_tags as mht')
      ->where('m.id = mht.mark_id')
      ->and_where(['m.author' => $user->id, 'mht.tag_id' => $tag->id]);
    if (empty($params['private'])) {
      $query->and_where(['m.visibility' => 0]);
    }
    return $query;
  }

}

class mark extends ressource
{

  use
  other_tables,
  dynamic_properties;

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
    if (is_object($user) && $user->id == $this->user_id) {
      $classname .= ' own';
    }
    return $classname;
  }

  function user_id()
  {
    return (int)$this->attribute('author');
  }

  function author()
  {
    return $this->table('users')->get($this->user_id);
  }

  function link_id()
  {
    return (int)$this->attribute('related');
  }

  function related()
  {
    return $this->table('links')->get($this->link_id);
  }

  function tags()
  {
    return $this->table('marks_tags')->from_mark($this);
  }

  function screenshot()
  {
    if (!$screenshot = $this->attribute('screenshot')) {
      $query = $this->table('screenshots')
        ->select('url')
        ->where(['link' => $this->attribute('related'), 'status' => 1])
        ->order_by('created DESC');
      $row = $query->fetch_one();
      $screenshot = $row ? $row['url'] : $this->default_screenshot();
      $this->cache_attribute('screenshot', $screenshot);
    }
    return $screenshot;
  }

  function default_screenshot()
  {
    $parsed_url = parse_url($this->url);
    return 'http://open.thumbshots.org/image.pxf?url=' . $parsed_url['host'];
  }

  function url()
  {
    if (!$url = $this->attribute('url')) {
      $url = $this->related->href;
      $this->cache_attribute('url', $url);
    }
    return $url;
  }

  function cache_attribute($key, $value)
  {
    $this->attributes[$key] = $value;
    $cache_key = $this->table('marks')->cache_key('id', $this->id);
    if ($row = cache::get($cache_key)) {
      $row[$key] = $value;
      cache::set($cache_key, $row);
    }
  }

}

return model('marks', table::instance('marks', __namespace__));
