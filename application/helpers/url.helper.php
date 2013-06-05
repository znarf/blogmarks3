<?php

function static_url($path = '')
{
  global $app, $request;
  return $request->protocol() . '://' . $request->host() . $app->path() . $path;
}
