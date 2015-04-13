<?php namespace blogmarks;

function title($base = null, $arg = null)
{
  if ($base) {
    blogmarks::$registry['title'] = $base;
    if ($arg) {
      blogmarks::$registry['title'] .= ' <span class="arg">' . $arg . '</span>';
    }
  }
  if (isset(blogmarks::$registry['title'])) {
    return blogmarks::$registry['title'];
  }
}
