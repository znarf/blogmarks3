<?php

$timezone = anonymous_class();

$format_offset = function($offset) {
  $hours = intval($offset / 3600);
  $minutes = abs(intval($offset % 3600 / 60));
  return 'UTC' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '+00:00');
};

$format_name = function($name) {
  $name = str_replace('/', ', ', $name);
  $name = str_replace('_', ' ', $name);
  $name = str_replace('St ', 'St. ', $name);
  return $name;
};

$timezone->list = function() use($format_name, $format_offset) {

  $offsets = $timezones = [];

  $now = new datetime;

  foreach (datetimezone::listidentifiers() as $timezone) {
    $now->setTimezone(new datetimezone($timezone));
    $offsets[] = $offset = $now->getOffset();
    $timezones[$timezone] = '(' . $format_offset($offset) . ') ' . $format_name($timezone);
  }

  array_multisort($offsets, $timezones);

  return $timezones;
};

$timezone->popular = [
  'Europe/Paris',
  'Europe/Berlin',
  'Europe/London',
  'America/New_York',
  'America/Los_Angeles',
  'Asia/Tokyo',
  'Asia/Hong_Kong'
];

return $timezone;
