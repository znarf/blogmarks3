<?php namespace blogmarks\model\table;

class links extends \blogmarks\model\table
{

  public $tablename = 'bm_links';

  public $unique_indexes = ['id', 'href'];

  # href contain special characters that can't make a valid memcache key
  # so we use an hash of the href as cache key
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

  function preload_for_marks($marks)
  {
    $marks = array_filter($marks, function($mark) { return !isset($mark->attributes['url']); });
    $link_ids = array_map(function($mark) { return (int)$mark->attributes['related']; }, $marks);
    if ($link_ids) {
      $this->get($link_ids);
    }
    /*
    $map = function($mark) { if (!isset($mark->attributes['url'])) return (int)$mark->attributes['related']; };
    $ids = array_filter(array_map($map, $marks));
    if ($ids) {
      $this->get($ids);
    }
    */
  }

}
