<?php namespace blogmarks\model\ressource;

class tag extends \blogmarks\model\ressource
{

  function __toString()
  {
    return $this->label;
  }

}
