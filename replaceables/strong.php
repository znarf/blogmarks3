<?php namespace blogmarks;

function strong($text)
{
  return '<strong>' . blogmarks::text($text) . '</strong>';
}
