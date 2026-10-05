<?php namespace blogmarks;

function authenticated_user($value = null)
{
  # Set
  if ($value) {
    blogmarks::$registry['user'] = $value;
  }
  # Cached
  if (isset(blogmarks::$registry['user'])) {
    return blogmarks::$registry['user'];
  }
  # Session Authenticated
  if (isset($_SESSION['user_id'])) {
    return blogmarks::$registry['user'] = blogmarks::table('users')->get($_SESSION['user_id']);
  }
  # Remember Me cookie, start a new session and extend the cookie
  if ($user = blogmarks::remembered_user()) {
    blogmarks::signin($user, true);
    return blogmarks::$registry['user'] = $user;
  }
}
