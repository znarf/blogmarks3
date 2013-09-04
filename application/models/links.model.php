<?php

use \Amateur\Model\Table as Table;
use \Amateur\Model\Ressource as Ressource;

class Links extends Table
{

  public $classname = 'Link';
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
    $ids = array_map(function($mark) { if (!$mark->attribute('url')) return (int)$mark->attribute('related'); }, $marks);
    $ids = array_filter($ids);
    $this->get($ids);
  }

}

class Link extends Ressource {}

return new Links;
