<?php

return function($value = null) {
  static $domain;
  return $value ? $domain = $value : ($domain ? $domain : 'public');
};
