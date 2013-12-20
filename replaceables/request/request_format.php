<?php

return function($value = null) {
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
  $accept = request_header('Accept');
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
};
