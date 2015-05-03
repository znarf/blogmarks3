<?php namespace blogmarks;

class registry
{

  static $services = [];

  static function service($name)
  {
    if (isset(self::$services[$name])) {
      return self::$services[$name];
    }
    else {
      return self::$services[$name] = blogmarks::instance("\\blogmarks\\service\\{$name}");
    }
  }

  static $models = [];

  static function model($name)
  {
    if (isset(self::$models[$name])) {
      return self::$models[$name];
    }
    else {
      return self::$models[$name] = blogmarks::instance("\\blogmarks\\model\\{$name}");
    }
  }

  static $tables = [];

  static function table($name)
  {
    if (isset(self::$tables[$name])) {
      return self::$tables[$name];
    }
    else {
      return self::$tables[$name] = blogmarks::instance("\\blogmarks\\model\\table\\{$name}");
    }
  }

  static $feeds = [];

  static function feed($name)
  {
    if (isset(self::$feeds[$name])) {
      return self::$feeds[$name];
    }
    else {
      return self::$feeds[$name] = blogmarks::instance("\\blogmarks\\model\\feed\\{$name}");
    }
  }

  static $searchs = [];

  static function search($name)
  {
    if (isset(self::$searchs[$name])) {
      return self::$searchs[$name];
    }
    else {
      return self::$searchs[$name] = blogmarks::instance("\\blogmarks\\model\\search\\{$name}");
    }
  }

}
