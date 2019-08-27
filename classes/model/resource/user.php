<?php namespace blogmarks\model\resource;

use amateur\amateur;

class user extends \blogmarks\model\resource
{

  use
  \amateur\magic\dynamic_properties;

  public $default_avatar = 'http://blogmarks.net/img/default-gravatar.gif';

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
    # would be better to not use replaceable here
    return amateur::web_url("/user/{$this->username}");
  }

  function avatar($size = 80)
  {
    $avatar = $this->attribute('avatar');
    if ($avatar && strpos($avatar, '@') !== false) {
      return $this->gravatar($avatar, $size);
    }
    /*
    if ($avatar && strpos($avatar, "http") === 0) {
      return $avatar;
    }
    */
    $email = $this->attribute('email');
    if ($email && strpos($email, '@') !== false) {
      return $this->gravatar($email, $size);
    }
    return $this->default_avatar;
  }

  function gravatar($email, $size = 80)
  {
    return 'http://www.gravatar.com/avatar.php?gravatar_id=' . md5($email) . '&size=' . $size . '&d=' . urlencode($this->default_avatar);
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
        if (flag('db_migrate_password')) {
          $this->table('users')->update($this, ['pass' => $password]);
        }
        return true;
      }
      return false;
    }
    # New shameless passwords
    return password_verify($password, $this->pass);
  }

  function generate_activation_key()
  {
    $activation_key = amateur::generate_phrase();
    $this->table('users')->update($this, ['activationkey' => $activation_key]);
    return $activation_key;
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
