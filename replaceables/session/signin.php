<?php namespace blogmarks;

function signin($user)
{
  $_SESSION['user_id'] = $user->id;
  return true;
}
