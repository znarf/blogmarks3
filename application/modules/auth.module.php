<?php

# Replaceables

$validate_signup_params = function() {
  check_parameters(['fullname', 'username', 'email', 'password', 'password_again']);
  if (filter_var(get_param('email'), FILTER_VALIDATE_EMAIL) === false) {
    form_error('email', "Email is invalid.");
  }
  elseif (filter_var(get_param('username'), FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => '/^[a-zA-Z][a-z\d_]{1,20}$/']]) === false) {
    form_error('username', "Username is invalid.");
  }
  elseif (table('users')->get_one('email', get_param('email'))) {
    form_error('email', "Email Address is taken.");
  }
  elseif (table('users')->get_one('login', get_param('username'))) {
    form_error('username', "Username is taken.");
  }
  elseif (get_param('password') != get_param('password_again')) {
    form_error('password', "Password doesn't match.");
  }
};

if (url_is('/auth/signin')) {
  domain('my');
  title('Sign In');
  if (is_post()) {
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
  return render('auth/signin');
}

elseif (url_is('/auth/signup')) {
  domain('my');
  title('Sign Up');
  if (is_post()) {
    $validate_signup_params();
    if (!form_error()) {
      $mark = table('users')->create([
        'name'       => get_param('fullname'),
        'login'      => get_param('username'),
        'email'      => get_param('email'),
        'pass'       => get_param('password')
      ]);
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