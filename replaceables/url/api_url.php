<?php

return function($path) {
  return 'http://' . request_host() . '/api' . $path;
};
