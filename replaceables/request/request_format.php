<?php namespace blogmarks;

function request_format($value = null)
{
  # Set
  if ($value) {
    blogmarks::$registry['request_format'] = $value;
  }
  # Cached
  if (isset(blogmarks::$registry['request_format'])) {
    return blogmarks::$registry['request_format'];
  }
  # Format Param
  $format = blogmarks::get_param('format');
  if ($format && in_array($format, ['json', 'rss', 'atom'])) {
    return blogmarks::$registry['request_format'] = $format;
  }
  # Accept Header
  $accept = blogmarks::request_header('Accept');
  $mime = [
    'application/json'     => 'json',
    'application/rss+xml'  => 'rss',
    'application/atom+xml' => 'atom'
  ];
  if ($accept && isset($mime[$accept])) {
    return blogmarks::$registry['request_format'] = $mime[$accept];
  }
  # Default
  return blogmarks::$registry['request_format'] = blogmarks::default_format();
};
