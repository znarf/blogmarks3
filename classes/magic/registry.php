<?php

namespace blogmarks\magic;

trait registry
{

  static function service($name)
  {
    return \blogmarks\registry::service($name);
  }

  static function model($name)
  {
    return \blogmarks\registry::model($name);
  }

  static function table($name)
  {
    return \blogmarks\registry::table($name);
  }

  static function feed($name)
  {
    return \blogmarks\registry::feed($name);
  }

  static function search($name)
  {
    return \blogmarks\registry::search($name);
  }

}
