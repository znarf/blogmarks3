<?php namespace blogmarks;

class container
{

  function marks($value = null)
  {
    return isset($value) ? $this->marks = $value : $this->marks;
  }

  function tags($value = null)
  {
    return isset($value) ? $this->tags = $value : $this->tags;
  }

  function users($value = null)
  {
    return isset($value) ? $this->users = $value : $this->users;
  }

  function __get($name)
  {
    if (isset(blogmarks::$registry['container'][$name])) {
      $value = blogmarks::$registry['container'][$name];
      return is_callable($value) ? $value() : $value;
    }
  }

  function __set($name, $value)
  {
    return blogmarks::$registry['container'][$name] = $value;
  }

}

return new container;
