<?php namespace blogmarks\model\table;

class users extends \blogmarks\model\table
{

  public $classname = '\blogmarks\model\ressource\user';

  public $tablename = 'bm_users';

  public $unique_indexes = ['id', 'login'];

  function create($set)
  {
    if (isset($set['pass'])) {
      $set['pass'] = password_hash($set['pass'], PASSWORD_DEFAULT);
    }
    return parent::create($set);
  }

  function update($where, $set)
  {
    if (isset($set['pass'])) {
      $set['pass'] = password_hash($set['pass'], PASSWORD_DEFAULT);
    }
    return parent::update($where, $set);
  }

}
