<?php

function alpha_prepare($tag)
{
  $accents = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
  $replacements = "aaaaaaaaaaaaooooooooooooeeeeeeeecciiiiiiiiuuuuuuuuynn";
  $tag->_alpha = strtr($tag->label, $accents, $replacements);
  return $tag;
}

function alpha_sort($a, $b)
{
  return strcmp($a->_alpha, $b->_alpha);
}

function taglist($tags = [])
{
  if (empty($tags)) return $tags;
  $maxPercent = '130';
  $minPercent = '60';
  array_map('alpha_prepare', $tags);
  usort($tags, 'alpha_sort');
  foreach ($tags as $tag) {
    $min = empty($min) || $min > $tag->count ? $tag->count : $min;
    $max = empty($max) || $max < $tag->count ? $tag->count : $max;
  }
  $diff = $max != $min ? $max - $min : 1;
  $multiplier = ($maxPercent-$minPercent)/($diff);
  $offset = $minPercent - $min*$multiplier;
  foreach ($tags as $tag) {
    $tag->_size = round( $tag->count*$multiplier + $offset );
  }
  return $tags;
}
