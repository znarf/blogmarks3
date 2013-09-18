<?php namespace blogmarks\model;

use
amateur\model\table,
amateur\model\ressource;

if ($instance = table::instance('links', __namespace__, true)) {
  return $instance;
}

class links extends table
{

  public $namespace = __namespace__;

  public $classname = 'link';

  public $tablename = 'bm_links';

  public $primary = 'id';

  public $unique_indexes = ['id'];

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

return table::instance('links', __namespace__);
