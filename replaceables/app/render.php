<?php namespace blogmarks;

function render($view, $args = [], $layout = 'default')
{
  if ($filename = blogmarks::filename('render', $view)) {
    extract($args);
    include $filename;
  }
  else {
    blogmarks::view($view, $args);
    blogmarks::layout($layout);
  }
  blogmarks::finish();
}
