<?php namespace blogmarks;

function rewrite_url($value)
{
  $request_url = blogmarks::request_url();
  if (strpos($request_url, '/marks/') === 0) {
    $url = str_replace('/marks', $value, $request_url);
  }
  elseif (strpos($request_url, '/my/marks/') === 0) {
    $url = str_replace('/my/marks', $value, $request_url);
  }
  elseif (strpos($request_url, '/my/friends/marks/') === 0) {
    $url = str_replace('/my/friends/marks', $value, $request_url);
  }
  else {
    $url = $value;
  }
  return blogmarks::relative_url($url == $request_url ? $value : $url);
}
