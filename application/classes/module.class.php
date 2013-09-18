<?php namespace blogmarks;

use
exception;

class module
{

  # Allow anonymous function set as object properties to be callable
  public function __call($method, $args)
  {
    if (is_callable([$this, $method])) {
      return call_user_func_array($this->$method, $args);
    }
    throw new exception('Unknown method/property.');
  }

}
