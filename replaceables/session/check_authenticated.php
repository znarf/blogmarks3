<?php namespace blogmarks;

function check_authenticated()
{
  if (!blogmarks::is_authenticated()) {
    blogmarks::response_code(401);
    return blogmarks::render('auth/signin');
  }
}
