<?php

return function($name, $value = null) {
  if ($value) if (!defined($name)) define($name, $value);
  if (defined($name)) return constant($name);
};
