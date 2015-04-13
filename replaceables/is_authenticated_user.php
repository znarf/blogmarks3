<?php namespace blogmarks;

function is_authenticated_user($check_user)
{
  if ($user = blogmarks::authenticated_user()) {
    if ($user->id == $check_user->id) {
      return true;
    }
  }
  return false;
}
