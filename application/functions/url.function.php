<?php

function relative_or_absolute_url($url)
{
  return request_format() == 'html' ? relative_url($url) : absolute_url($url);
}

function web_url($path)
{
  return 'http://' . request()->host() . $path;
}

function api_url($path)
{
  return 'http://' . request()->host() . '/api' . $path;
}
