<?php namespace blogmarks;

# Keep the user signed in for 30 days (requires the 'secret' flag)
function remember_user($user)
{
  if (!blogmarks::flag('secret')) {
    return false;
  }
  $expires = time() + 30 * 24 * 3600;
  blogmarks::remember_cookie(blogmarks::remember_token($user, $expires), $expires);
  return true;
}
