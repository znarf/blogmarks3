<?php namespace blogmarks;

function signout()
{
  $_SESSION['user_id'] = null;
  $_SESSION['oauth_access_token'] = null;
  $_SESSION['oauth_authenticated_user'] = null;
  return true;
}
