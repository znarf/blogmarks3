<?php

use \Amateur\Model\Table as Table;
use \Amateur\Model\Ressource as Ressource;
use \Amateur\Model\Dynamize as Dynamize;

class Links extends Table
{

  public $classname = 'Link';
  public $tablename = 'bm_links';
  public $primary = 'id';
  public $unique_indexes = ['id'];

  function with_url($url)
  {
    return ($link = self::get_one('href', $url)) ? $link : self::create(['href' => $url]);
  }

}

class Link extends Ressource
{

  use Dynamize;

  /*
  function screenshot()
  {
    $result = model('screenshots')->get_one('link', $this->id);
    return $result && $result->status == 1 ? $result : null;
  }
  */

}

return instance('Links');
