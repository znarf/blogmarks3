<?php namespace blogmarks;

function is_bookmarklet()
{
  return blogmarks::get_param('bookmarklet', blogmarks::get_param('mini'));
}
