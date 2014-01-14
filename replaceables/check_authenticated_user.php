<?php

return function($check_user) {
  if (!is_authenticated_user($check_user)) {
    throw http_error(403, 'Forbidden.');
  }
};
