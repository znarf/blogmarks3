<?php namespace blogmarks;

function signin($user, $remember = false)
{
  # New session id, prevents session fixation
  session_regenerate_id(true);
  $_SESSION['user_id'] = $user->id;
  if ($remember) {
    blogmarks::remember_user($user);
  }
  return true;
}
