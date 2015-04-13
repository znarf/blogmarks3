<?php namespace blogmarks;

function check_authenticated_user($check_user)
{
  if (!blogmarks::is_authenticated_user($check_user)) {
    throw blogmarks::http_error(403, 'Forbidden.');
  }
}
