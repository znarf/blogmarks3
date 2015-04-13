<?php namespace blogmarks;

function web_url($path)
{
  return 'http://' . blogmarks::request_host() . $path;
}
