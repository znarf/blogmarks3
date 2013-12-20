<?php

return function() {
  return [
    'offset' => get_int('offset', 0),
    'limit'  => get_int('limit', 10),
    'order'  => get_param('order', 'desc'),
    'after'  => get_param('after'),
    'before' => get_param('before')
  ];
};
