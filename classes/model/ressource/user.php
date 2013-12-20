<?php namespace blogmarks\model\ressource;

class user extends \blogmarks\model\ressource
{

  use
  \amateur\model\dynamic_properties;

  function username()
  {
    return $this->attribute('login');
  }

  function name()
  {
    $name = $this->attribute('name');
    return empty($name) ? $this->username : $name;
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
    return $this->table('marks')->fetch_object(['related' => $link->id, 'author' => $this->id]);
  }

  function verify_passsword($password)
  {
    # Compatibility with 1st generation of passwords (ouch !)
    if (preg_match('/^[a-f0-9]{32}$/', $this->pass)) {
      if ($this->pass == md5($password)) {
        # Re-hash it on the fly
        if (!flag('db_read_only')) {
          $this->table('users')->update($this, ['pass' => $password]);
        }
        return true;
      }
      return false;
    }
    # New shameless passwords
    return password_verify($password, $this->pass);
  }

}
