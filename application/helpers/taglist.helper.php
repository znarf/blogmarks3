<?php

$taglist = anonymous_class();

$sort_prepare = function($str) {
  $str = iconv('utf-8', 'ASCII//TRANSLIT//IGNORE', $str);
  return strtolower($str);
};

$sort_tags = function($tags) use($sort_prepare) {
  $_tags = [];
  foreach ($tags as $tag) {
    $_tags[$sort_prepare($tag->label)] = $tag;
  }
  ksort($_tags);
  return array_values($_tags);
};

$taglist->compute = function($tags = []) use($sort_tags) {
  if (count($tags) < 1) {
    return $tags;
  }
  $min_percent = 70;
  $max_percent = 130;
  foreach ($tags as $tag) {
    $min = empty($min) || $min > $tag->count ? $tag->count : $min;
    $max = empty($max) || $max < $tag->count ? $tag->count : $max;
  }
  $diff = $max != $min ? $max - $min : 1;
  $multiplier = ($max_percent - $min_percent) / $diff;
  $offset = $min_percent - $min * $multiplier;
  foreach ($tags as $tag) {
    $tag->_size = round($tag->count * $multiplier + $offset);
  }
  return $sort_tags($tags);
};

return $taglist;
