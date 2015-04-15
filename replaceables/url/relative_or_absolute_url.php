<?php namespace blogmarks;

function relative_or_absolute_url($url)
{
  return blogmarks::request_format() == 'html' ? blogmarks::relative_url($url) : blogmarks::absolute_url($url);
}
