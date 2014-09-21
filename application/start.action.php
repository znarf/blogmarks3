<?php

start_session();

if ($user = authenticated_user()) {
  user_settings($user);
}

if (url_is('/')) {
  redirect('/marks');
}
elseif (url_start_with('/my/tools')) {
  module('tools');
}
elseif (url_start_with('/my/profile')) {
  module('profile');
}
elseif (url_start_with('/my/friends')) {
  module('friends');
}
elseif (url_start_with('/my')) {
  module('my');
}
elseif (url_start_with('/auth')) {
  module('auth');
}
else {
  module('public');
}
