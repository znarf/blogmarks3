<?php

function ok($content)
{
  echo $content;
}

function text($text)
{
  return htmlspecialchars($text, ENT_QUOTES | ENT_HTML5);
}

function html($html)
{
  return $html;
}

function arg($string)
{
  return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5);
}

function strong($text)
{
  return '<strong>' . text($text) . '</strong>';
}
