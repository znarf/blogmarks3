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
    check_token('update_profile', get_param('token'));
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
  $params += [
    'update_profile_token' => generate_token('update_profile'),
    'update_password_token' => generate_token('update_password')
  ];
  return render('profile/index', $params);
}

elseif ($matches = url_match('/my/profile,password')) {
  if (is_post()) {
    check_token('update_password', get_param('token'));
    check_parameters(['password_current', 'password_new', 'password_new_confirm']);
    if (!get_param('password_current') || !$user->verify_passsword(get_param('password_current'))) {
      flash_message( _('Current password is invalid.') );
    }
    elseif (!get_param('password_new')) {
      flash_message( _("New password can't be empty.") );
    }
    elseif (get_param('password_new') !== get_param('password_new_confirm')) {
      flash_message( _("New password doesn't match confirmation.") );
    }
    else {
      table('users')->update($user, ['pass' => get_param('password_new')]);
      flash_message( _('Password Updated.') );
    }
  }
  return redirect('/my/profile,general');
}


else {
  return unknown_url();
}
