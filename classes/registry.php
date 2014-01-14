<?php namespace blogmarks;

class registry extends \amateur\core\registry
{

  static function service($name)
  {
    return self::instance('service', $name, "\\blogmarks\\service\\{$name}");
  }

  static function model($name)
  {
    return self::instance('model', $name, "\\blogmarks\\model\\{$name}");
  }

  static function table($name)
  {
    return self::instance('table', $name, "\\blogmarks\\model\\table\\{$name}");
  }

  static function feed($name)
  {
    return self::instance('feed', $name, "\\blogmarks\\model\\feed\\{$name}");
  }

  static function search($name)
  {
    return self::instance('search', $name, "\\blogmarks\\model\\search\\{$name}");
  }

}
