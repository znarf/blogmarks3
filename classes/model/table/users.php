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

  function validate_field($key, $value, $current_user = null)
  {
    switch ($key) {
      case 'email':
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
          return 'Email is invalid';
        }
        if ($other_user = $this->get_one('email', $value)) {
          if (!$current_user || $other_user->id != $current_user->id) {
            return 'Email is taken';
          }
        }
        break;
      case 'login':
        if (filter_var($value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[a-zA-Z][a-z\d_]{1,20}$/']]) === false) {
          return 'Username is invalid';
        }
        if ($other_user = $this->get_one('login', $value)) {
          if (!$current_user || $other_user->id != $current_user->id) {
            return 'Username is taken';
          }
        }
        break;
    }
  }

}
