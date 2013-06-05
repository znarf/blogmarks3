<?php

if (url_is('/auth/signin')) {
  domain('my');
  title('Sign In');
  if (is_post()) {
    check_parameters(['username', 'password']);
    # Find User
    foreach (['email', 'login'] as $key) {
      if ($user = model('users')->get_one($key, get_param('username'))) break;
    }
    # If User Found
    if ($user && $user->verify_passsword(get_param('password'))) {
      signin($user);
      return redirect(get_param('redirect_url', '/my/'));
    }
    # Invalid User or Password
    throw http_error(401, 'Unknown username/email or invalid password.');
  }
  layout(view('auth/signin'));
}

if (url_is('/auth/signout')) {
  signout();
  return redirect('/');
}
