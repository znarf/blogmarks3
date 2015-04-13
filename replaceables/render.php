<?php namespace blogmarks;

function render($name, $args = [], $layout = 'default')
{
  if ($name == 'marks') {
    blogmarks::helper('render')->marks($args);
  }
  else {
    blogmarks::view($name, $args);
    blogmarks::layout($layout);
  }
  blogmarks::finish();
}
