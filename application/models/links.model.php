<?php namespace blogmarks\model;

use
amateur\model\table,
amateur\model\ressource;

class links extends table
{

  public $namespace = __namespace__;

  public $classname = 'link';

  public $tablename = 'bm_links';

  public $primary = 'id';

  public $unique_indexes = ['id', 'href'];

  function cache_key($key, $value, $type = 'raw')
  {
    if ($key == 'href') {
      $value = md5($value);
      return "{$this->tablename}_{$type}_{$key}_{$value}";
    }
    else {
      return parent::cache_key($key, $value, $type);
    }
  }

  function with_url($url)
  {
    $link = self::get_one('href', $url);
    return $link ? $link : self::create(['href' => $url]);
  }

  function load_from_marks($marks)
  {
    $map = function($mark) { if (!isset($mark->attributes['url'])) return (int)$mark->attributes['related']; };
    $ids = array_map($map, $marks);
    $ids = array_filter($ids);
    $this->get($ids);
  }

}

class link extends ressource {}

return model('links', table::instance('links', __namespace__));
