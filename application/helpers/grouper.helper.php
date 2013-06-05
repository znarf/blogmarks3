<?php namespace Blogmarks\Helper;

class Grouper
{

  static function group($marks = [])
  {
    $groups = [];

    $first_mark = reset($marks);
    $last_mark = end($marks);

    $range = strtotime($first_mark->published) - strtotime($last_mark->published);

    if ($range > 2 * 30 * 24 * 3600) {
      $group_marker = 'group_marker_month';
    } else if ($range > 2 * 24 * 3600) {
      $group_marker = 'group_marker_day';
    } else {
      $group_marker = 'group_marker_hour';
    }

    foreach ($marks as $mark) {
      $marker = self::$group_marker(strtotime($mark->published));
      if (empty($groups[$marker])) {
        $groups[$marker] = [$mark];
      } else {
        $groups[$marker][] = $mark;
      }
    }

    return $groups;
  }

  static function group_marker_month($timestamp)
  {
    return strftime('%B %Y', $timestamp);
  }

  static function group_marker_day($timestamp)
  {
    $format = '%d %B %Y';

    static $today;
    static $yesterday;
    isset($today) || $today = strftime($format);
    isset($yesterday) || $yesterday = strftime($format, time() - 24 * 3600);

    $marker = strftime($format, $timestamp);
    return $marker == $today ? _('Today') : ($marker == $yesterday ? _('Yesterday') : $marker);
  }

  static function group_marker_hour($timestamp)
  {
    return strftime('%d %B %Y %H:00', $timestamp);
  }

}

return new Grouper;
