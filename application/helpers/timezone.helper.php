<?php namespace blogmarks;

use
DateTime as date_time,
DateTimeZone as date_time_zone;

class timezone
{

  public $popular = [
    'Europe/Paris',
    'Europe/Berlin',
    'Europe/London',
    'America/New_York',
    'America/Los_Angeles',
    'Asia/Tokyo',
    'Asia/Hong_Kong'
  ];

  function format_offset($offset)
  {
    $hours = intval($offset / 3600);
    $minutes = abs(intval($offset % 3600 / 60));
    return 'UTC' . ($offset ? sprintf('%+03d:%02d', $hours, $minutes) : '+00:00');
  }

  function format_name($name)
  {
    $name = str_replace('/', ', ', $name);
    $name = str_replace('_', ' ', $name);
    $name = str_replace('St ', 'St. ', $name);
    return $name;
  }

  function all()
  {
    $offsets = $timezones = [];

    $now = new date_time;

    foreach (date_time_zone::listidentifiers() as $timezone) {
      $now->setTimezone(new date_time_zone($timezone));
      $offsets[] = $offset = $now->getOffset();
      $timezones[$timezone] = '(' . $this->format_offset($offset) . ') ' . $this->format_name($timezone);
    }

    array_multisort($offsets, $timezones);

    return $timezones;
  }

}

return new timezone;
