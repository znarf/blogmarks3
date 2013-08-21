<?php

use \Amateur\Model\Table as Table;
use \Amateur\Model\Ressource as Ressource;
use \Amateur\Model\Dynamize as Dynamize;

class Users extends Table
{

  public $classname = 'User';
  public $tablename = 'bm_users';
  public $primary = 'id';
  public $unique_indexes = ['id', 'login'];

}

class User extends Ressource
{

  use Dynamize;

  function username()
  {
    return $this->attribute('login');
  }

  function name()
  {
    $name = $this->attribute('name');
    return empty($name) ? $this->attribute('login') : $name;
  }

  function url()
  {
    return web_url("/user/{$this->username}");
  }

  function avatar($size = 80)
  {
    $avatar = $this->attribute('avatar');
    if (strpos($avatar, "@") !== false) {
      return 'http://www.gravatar.com/avatar.php?gravatar_id=' . md5($avatar) . '&size=' . $size;
    /*
    } elseif (strpos($avatar, "http") === 0) {
      return $avatar;
    */
    } else {
      return absolute_url() . '/img/default-gravatar.gif';
    }
  }

  function mark_with_link($link)
  {
    if (is_string($link)) {
      $link = model('links')->with_url($link);
    }
    return model('marks')->find_one(['related' => $link->id, 'author' => $this->id]);
  }

  function verify_passsword($password)
  {
    require_once root_dir . '/lib/password.php';
    if (preg_match('/^[a-f0-9]{32}$/', $this->pass)) {
      if ($this->pass == md5($password)) {
        if (!flag('db_read_only')) model('users')->update($this, ['pass' => password_hash($password, PASSWORD_DEFAULT)]);
        return true;
      }
      return false;
    }
    return password_verify($password, $this->pass);
  }

}

return instance('Users');
