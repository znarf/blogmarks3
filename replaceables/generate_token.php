<?php

return function($key) {
  return $_SESSION["csrf_{$key}"] = generate_phrase();
};
