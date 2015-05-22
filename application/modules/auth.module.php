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
      signin($user);
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
      return redirect('/my/');
    }
  }
  return render('auth/signup', get_parameters(['fullname', 'username', 'email']));
}

elseif (url_is('/auth/signout')) {
  signout();
  return redirect('/');
}

else {
  return unknown_url();
}
