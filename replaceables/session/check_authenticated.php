<?php

return function() {
  if (!is_authenticated()) {
    response_code(401);
    render('/auth/signin');
  }
};
