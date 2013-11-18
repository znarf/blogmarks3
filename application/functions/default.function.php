<?php

function flag($name, $value = null)
{
  if ($value) if (!defined($name)) define($name, $value);
  if (defined($name)) return constant($name);
}

function action($name, $args = [])
{
  extract($args);
  include app()->filename('action', $name);
}

function default_format($value = null)
{
  static $default_format;
  return $value ? $default_format = $value : ($default_format ? $default_format : 'html');
}

function request_format($value = null)
{
  static $request_format;
  # Set
  if ($value) {
    $request_format = $value;
  }
  # Cached
  if ($request_format) {
    return $request_format;
  }
  # Format Param
  $format = get_param('format');
  if ($format && in_array($format, ['json', 'rss', 'atom'])) {
    return $request_format = $format;
  }
  # Accept Header
  $accept = request()->header('Accept');
  $mime = [
    'application/json'     => 'json',
    'application/rss+xml'  => 'rss',
    'application/atom+xml' => 'atom'
  ];
  if ($accept && isset($mime[$accept])) {
    return $request_format = $mime[$accept];
  }
  # Default
  return $request_format = default_format();
}

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
    $side_title = strong($base);
    if ($arg) {
      $side_title .= ' ' . $arg;
    }
  }
  return $side_title ? $side_title : '<strong>Public</strong> Tags';
}

function tags_title($base = null, $arg = null)
{
  return side_title($base, $arg);
}
