<?php

return function($name, $args = []) {
  if ($name == 'marks') {
    helper('render')->marks($args);
  }
  else {
    layout(view($name, $args));
  }
  finish();
};
