<?php namespace blogmarks;

class grouper
{

  static $today;

  static $yesterday;

  function marker_month($timestamp)
  {
    return strftime('%B %Y', $timestamp);
  }

  function marker_day($timestamp)
  {
    $format = '%d %B %Y';

    $today = self::$today ?: self::$today = strftime($format);
    $yesterday = self::$yesterday ?: self::$yesterday = strftime($format, time() - 24 * 3600);

    $marker = strftime($format, $timestamp);
    return $marker == $today ? _('Today') : ($marker == $yesterday ? _('Yesterday') : $marker);
  }

  function marker_hour($timestamp)
  {
    return strftime('%d %B %Y %H:00', $timestamp);
  }

  function group($marks = [])
  {
    $groups = [];

    $first_mark = reset($marks);
    $last_mark = end($marks);

    $range = $first_mark->published->getTimestamp() - $last_mark->published->getTimestamp();

    if ($range > 2 * 30 * 24 * 3600) {
      $group_marker = [$this, 'marker_month'];
    }
    elseif ($range > 2 * 24 * 3600) {
      $group_marker = [$this, 'marker_day'];
    }
    else {
      $group_marker = [$this, 'marker_hour'];
    }

    foreach ($marks as $mark) {
      $marker = $group_marker($mark->published->getTimestamp());
      $groups[$marker][] = $mark;
    }

    return $groups;
  }

}

return new grouper;
