<?php namespace blogmarks;

class taglist
{

  function sort_prepare($str)
  {
    $str = iconv('utf-8', 'ASCII//TRANSLIT//IGNORE', $str);
    return strtolower($str);
  }

  function sort_tags($tags)
  {
    $sorted_tags = [];
    foreach ($tags as $tag) {
      $key = $this->sort_prepare($tag->label);
      $sorted_tags[$key] = $tag;
    }
    ksort($sorted_tags);
    return array_values($sorted_tags);
  }

  function compute($tags = [])
  {
    if (count($tags) < 1) {
      return $tags;
    }

    $min_percent = 70;
    $max_percent = 130;

    $min = min(array_column($tags, 'count'));
    $max = max(array_column($tags, 'count'));

    $diff = $max != $min ? $max - $min : 1;
    $multiplier = ($max_percent - $min_percent) / $diff;
    $offset = $min_percent - $min * $multiplier;

    foreach ($tags as $tag) {
      $tag->_size = round($tag->count * $multiplier + $offset);
    }

    return $this->sort_tags($tags);
  }

}

return new taglist;
