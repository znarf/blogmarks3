<?php

return function($name, $args = []) {
  extract($args);
  include filename('action', $name);
};
