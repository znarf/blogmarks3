<?php

return function($url) {
  return request_format() == 'html' ? relative_url($url) : absolute_url($url);
};
