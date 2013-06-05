<?php

function start_session()
{
  if (!session_id()) {
    session_start();
  }
}

function is_authenticated()
{
  return isset($_SESSION['user_id']);
}

function signin($user)
{
  $_SESSION['user_id'] = $user->id;
  return true;
}

function authenticated_user()
{
  if (is_authenticated()) {
    return app()->model('users')->get($_SESSION['user_id']);
  }
}

function flash_message($message = null)
{
  if ($message) {
    return $_SESSION['message'] = $message;
  }
  elseif (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
    return $message;
  }
}

function signout()
{
  $_SESSION['user_id'] = null;
  return true;
}

function check_authenticated()
{
  if (!is_authenticated()) {
    status(401);
    render('/auth/signin');
  }
}

function is_authenticated_user($check_user)
{
  if ($user = authenticated_user()) {
    if ($user->id == $check_user->id) {
      return true;
    }
  }
  return false;
}

function check_authenticated_user($check_user)
{
  if ($user = authenticated_user()) {
    if ($user->id == $check_user->id) {
      return true;
    }
  }
  throw http_error(403, 'Forbidden.');
}
