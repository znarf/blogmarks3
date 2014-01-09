<?php

domain('my');

title('Profile');

check_authenticated();
$user = authenticated_user();

section('tools');

if (url_is('/my/profile')) {
  return redirect('/my/profile,general');
}

elseif ($matches = url_match('/my/profile/*,edit')) {
  return redirect('/my/profile,' . $matches[1]);
}

elseif ($matches = url_match('/my/profile,*')) {
  if (is_post()) {
    flash_message( _('Profile Updated') );
  }
  return render('profile/index', ['action' => $matches[1], 'user' => $user]);
}

else {
  return unknown_url();
}
