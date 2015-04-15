<?php namespace blogmarks;

function signout()
{
  $_SESSION['user_id'] = null;
  return true;
}
