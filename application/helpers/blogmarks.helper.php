<?php

function brand($value = null)
{
  static $brand;
  return $value ? $brand = $value : ($brand ? $brand : 'Blogmarks');
}

function domain($value = null)
{
  static $domain;
  return $value ? $domain = $value : ($domain ? $domain : 'public');
}

function section($value = null)
{
  static $section;
  return $value ? $section = $value : ($section ? $section : 'public');
}

function title($base = null, $arg = null)
{
  static $title;
  if ($base) {
    $title = $base;
    if ($arg) {
      $title .= ' <span class="arg">' . $arg . '</span>';
    }
  }
  return $title;
}

function side_title($base = null, $arg = null)
{
  static $side_title;
  if ($base) {
    $side_title = '<strong>' . $base . '</strong>';
  }
  if ($arg) {
    $side_title .= ' ' . $arg;
  }
  return $side_title ? $side_title : '<strong>Public</strong> Tags';
}

function tags_title($base = null, $arg = null)
{
  return side_title($base, $arg);
}

function relative_or_absolute_url($url)
{
  global $app;
  return $app->request_format() == 'html' ? relative_url($url) : absolute_url($url);
}

function web_url($path)
{
  $app = core('app');
  return 'http://' . $app->request()->host() . $path;
}

function api_url($path)
{
  $app = core('app');
  return 'http://' . $app->request()->host() . '/api' . $path;
}
