<?php namespace blogmarks;

function api_url($path)
{
  return 'http://' . blogmarks::request_host() . '/api' . $path;
}
