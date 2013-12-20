<?php

return function($user) {
  $_SESSION['user_id'] = $user->id;
  return true;
};
