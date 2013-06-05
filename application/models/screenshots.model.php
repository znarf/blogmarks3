<?php

use \Amateur\Model\Table as Table;
use \Amateur\Model\Ressource as Ressource;

class Screenshots extends Table
{

  public $classname = 'Screenshot';
  public $tablename = 'bm_screenshots';
  public $primary = 'id';
  public $unique_indexes = ['id'];

}

class Screenshot extends Ressource {};

return function() { static $instance; return $instance ? $instance : $instance = new Screenshots; };