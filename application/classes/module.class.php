<?php namespace Blogmarks;

class Module
{

  # Allow anonymous function set as object properties to be callable
  public function __call($method, $args)
  {
    if (is_callable([$this, $method])) {
      return call_user_func_array($this->$method, $args);
    }
    throw new Exception('Unknown method/property.');
  }

}
