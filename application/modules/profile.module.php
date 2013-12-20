<?php

domain('my');

title('Profile');

check_authenticated();
$user = authenticated_user();

section('tools');

if (url_is('/my/profile')) {
  redirect('/my/profile,general');
}

elseif ($matches = url_match('/my/profile/*,edit')) {
  redirect('/my/profile,' . $matches[1]);
}

/*
elseif (url_is('/my/profile/general')) {
  render('profile/index', ['action' => 'general']);
}
*/

elseif ($matches = url_match('/my/profile,*')) {
  if (is_post()) {
    flash_message( _('Profile Updated') );
  }
  render('profile/index', ['action' => $matches[1], 'user' => $user]);
}

else {
  unknown_url();
}
