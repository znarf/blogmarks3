<?php

return function($value = null) {
  static $brand;
  return $value ? $brand = $value : ($brand ? $brand : 'Blogmarks');
};
