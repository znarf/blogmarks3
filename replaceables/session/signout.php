<?php namespace blogmarks;

function signout()
{
  $_SESSION['user_id'] = null;
  if (isset($_COOKIE['remember'])) {
    blogmarks::remember_cookie();
  }
  return true;
}
