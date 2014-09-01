<?php namespace blogmarks\model\resource;

class user extends \blogmarks\model\resource
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
    $email = $this->attribute('email');
    if ($email && strpos($email, "@") !== false) {
      return 'http://www.gravatar.com/avatar.php?gravatar_id=' . md5($email) . '&size=' . $size;
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

  function following_ids()
  {
    return $this->table('user_relations')->where(['user' => $this->id])->fetch_ids('contact');
  }

  function follower_ids()
  {
    return $this->table('user_relations')->where(['contact' => $this->id])->fetch_ids('user');
  }

  function __toString()
  {
    return $this->name();
  }

}
