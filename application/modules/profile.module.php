<?php

domain('my');

title(_('Profile'));

check_authenticated();
$user = authenticated_user();

section('profile');

if (url_is('/my/profile')) {
  return redirect('/my/profile,general');
}

elseif ($matches = url_match('/my/profile/*,edit')) {
  return redirect('/my/profile,' . $matches[1]);
}

elseif ($matches = url_match('/my/profile,general')) {
  if (is_post()) {
    check_parameters(['name', 'login', 'email']);
    $params = [
      'name'       => get_param('name'),
      'login'      => get_param('login'),
      'email'      => get_param('email'),
      'lang'       => get_param('lang'),
      'timezone'   => get_param('timezone')
    ];
    foreach ($params as $key => $value) {
      if ($error = table('users')->validate_field($key, $value, $user)) {
        form_error($key, $error);
      }
    }
    if (!form_error()) {
      $user = table('users')->update($user, $params);
      flash_message( _('Profile Updated') );
    }
  }
  else {
    $params = [
      'name'     => $user->name,
      'email'    => $user->email,
      'login'    => $user->login,
      'lang'     => $user->lang,
      'timezone' => $user->timezone
    ];
  }
  return render('profile/index', $params);
}

else {
  return unknown_url();
}
