<?php namespace blogmarks\model\resource;

class tag extends \blogmarks\model\resource
{

  function __toString()
  {
    return $this->label;
  }

}
