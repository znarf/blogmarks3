<?php namespace blogmarks;

# User from a valid "remember me" cookie, an invalid cookie is deleted
function remembered_user()
{
  if (!blogmarks::flag('secret') || empty($_COOKIE['remember'])) {
    return;
  }
  $cookie = $_COOKIE['remember'];
  $parts = is_string($cookie) ? explode(':', $cookie) : [];
  if (count($parts) == 3 && ctype_digit($parts[0]) && ctype_digit($parts[1]) && $parts[1] >= time()) {
    $user = blogmarks::table('users')->get((int)$parts[0]);
    if ($user && hash_equals(blogmarks::remember_token($user, $parts[1]), $cookie)) {
      return $user;
    }
  }
  blogmarks::remember_cookie();
  unset($_COOKIE['remember']);
}
