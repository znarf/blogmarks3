<?php namespace blogmarks;

function signin($user, $remember = false)
{
  $_SESSION['user_id'] = $user->id;
  return true;
}
