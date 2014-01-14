<?php

return function($name, $args = [], $layout = 'default') {
  if ($name == 'marks') {
    helper('render')->marks($args);
  }
  else {
    layout($layout, view($name, $args));
  }
  finish();
};
