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

elseif ($matches = url_match('/my/profile,general')) {
  if (is_post()) {
    check_parameters(['fullname', 'username', 'email']);
    $params = [
      'name'       => get_param('fullname'),
      'login'      => get_param('username'),
      'email'      => get_param('email')
    ];
    foreach ($params as $key => $value) {
      if ($error = table('users')->validate_field($key, $value, $user)) {
        form_error($key, $error);
      }
    }
    if (!form_error()) {
      $user = table('users')->update($user, $params);
    }
    flash_message( _('Profile Updated') );
  }
  return render('profile/index', ['fullname' => $user->name, 'email' => $user->email, 'username' => $user->login]);
}

else {
  return unknown_url();
}
