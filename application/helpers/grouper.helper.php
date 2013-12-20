<?php

$grouper = anonymous_class();

$marker_month = function($timestamp) {
  return strftime('%B %Y', $timestamp);
};

$marker_day = function($timestamp) {
  $format = '%d %B %Y';

  static $today;
  static $yesterday;
  isset($today) || $today = strftime($format);
  isset($yesterday) || $yesterday = strftime($format, time() - 24 * 3600);

  $marker = strftime($format, $timestamp);
  return $marker == $today ? _('Today') : ($marker == $yesterday ? _('Yesterday') : $marker);
};

$marker_hour = function($timestamp) {
  return strftime('%d %B %Y %H:00', $timestamp);
};

$grouper->group = function($marks = []) use($marker_month, $marker_day, $marker_hour) {
  $groups = [];

  $first_mark = reset($marks);
  $last_mark = end($marks);

  $range = strtotime($first_mark->published) - strtotime($last_mark->published);

  if ($range > 2 * 30 * 24 * 3600) {
    $group_marker = $marker_month;
  }
  elseif ($range > 2 * 24 * 3600) {
    $group_marker = $marker_day;
  }
  else {
    $group_marker = $marker_hour;
  }

  foreach ($marks as $mark) {
    $marker = $group_marker(strtotime($mark->published));
    $groups[$marker][] = $mark;
  }

  return $groups;
};

return $grouper;
