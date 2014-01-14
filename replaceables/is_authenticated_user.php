<?php

return function($check_user) {
  if ($user = authenticated_user()) {
    if ($user->id == $check_user->id) {
      return true;
    }
  }
  return false;
};
