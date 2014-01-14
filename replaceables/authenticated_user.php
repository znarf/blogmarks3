<?php

return function() {
  if (is_authenticated()) {
    static $user;
    return $user ? $user : ($user = table('users')->get($_SESSION['user_id']));
  }
};
