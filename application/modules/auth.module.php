<?php

if (url_is('/auth/signin')) {
  domain('my');
  title(_('Sign In'));
  if (is_post()) {
    check_token('sign_in', get_param('token'));
    check_parameters(['username', 'password']);
    # Find user
    foreach (['email', 'login'] as $key) {
      if ($user = table('users')->get_one($key, get_param('username'))) break;
    }
    # If user found
    if ($user && $user->verify_passsword(get_param('password'))) {
      signin($user, get_bool('remember'));
      return redirect(get_param('redirect_url', '/my/'));
    }
    # Invalid user or password
    response_code(401);
    flash_message('Unknown username/email or invalid password.');
  }
  # CSRF token
  $params = ['token' => generate_token('sign_in')];
  return render('auth/signin', $params);
}

elseif (url_is('/auth/signup')) {
  if (!flag('enable_signup')) {
    return error(200, 'Sign Up is currently disabled.');
  }
  domain('my');
  title(_('Sign Up'));
  if (is_post()) {
    check_parameters(['fullname', 'username', 'email', 'password', 'password_again']);
    $params = [
      'name'       => get_param('fullname'),
      'login'      => get_param('username'),
      'email'      => get_param('email'),
      'pass'       => get_param('password')
    ];
    foreach ($params as $key => $value) {
      if ($error = table('users')->validate_field($key, $value)) {
        form_error($key, $error);
      }
    }
    if (get_param('password') != get_param('password_again')) {
      form_error('password', "Password doesn't match.");
    }
    if (!form_error()) {
      $user = table('users')->create($params);
      signin($user);
      flash_message('Sign up successful!');
      return redirect('/my/');
    }
    else {
      response_code(401);
    }
  }
  return render('auth/signup', get_parameters(['fullname', 'username', 'email']));
}

if (url_is('/auth/forgot-password')) {
  domain('my');
  title(_('Forgot Password?'));
  if (is_post()) {
    check_token('forgot_password', get_param('token'));
    check_parameters(['username']);
    # Find user
    foreach (['email', 'login'] as $key) {
      if ($user = table('users')->get_one($key, get_param('username'))) break;
    }
    # If user found
    if ($user) {
      $key = $user->generate_activation_key();

      $email  = _('Someone has asked to reset the password for the following site and username.') . "\n\n";
      $email .= '- ' . _('Site:') . ' ' . absolute_url('/') . "\n";
      $email .= '- ' . _('Username:') . ' ' . $user->login . "\n\n";
      $email .= _('To reset your password visit the following address, otherwise just ignore this email and nothing will happen.') . "\n\n";
      $email .= absolute_url("/auth/reset-password?key=" . $key);

      service('email')->send($user->email, 'Reset Password', $email);

      return render('auth/forgot-password', ['success' => true]);
    }
    # Invalid user
    flash_message( _('Unknown username or email.') );
  }
  # CSRF token
  $params = ['token' => generate_token('forgot_password')];
  return render('auth/forgot-password', $params);
}

if (url_is('/auth/reset-password')) {
  domain('my');
  title(_('Reset Password'));
  check_parameters(['key']);
  $user = table('users')->get_one('activationkey', get_param('key'));
  if (!$user) {
    return render('auth/reset-password', ['invalid' => true]);
  }
  if (is_post()) {
    check_token('reset_password', get_param('reset_password_token'));
    check_parameters(['password_new', 'password_new_confirm']);
    if (!get_param('password_new')) {
      flash_message( _("New password can't be empty.") );
    }
    elseif (get_param('password_new') !== get_param('password_new_confirm')) {
      flash_message( _("New password doesn't match confirmation.") );
    }
    else {
      table('users')->update($user, ['pass' => get_param('password_new')]);
      return render('auth/reset-password', ['success' => true]);
    }
  }
  # CSRF token
  $params = ['reset_password_token' => generate_token('reset_password')];
  return render('auth/reset-password', $params);
}

elseif (url_is('/auth/signout')) {
  signout();
  return redirect('/');
}

else {
  return unknown_url();
}
