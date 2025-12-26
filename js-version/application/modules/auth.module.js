module.exports = function () {
  if (url_is('/auth/signin')) {
    domain('my');
    title(_('Sign In'));
    if (is_post()) {
      check_token('sign_in', get_param('token'));
      check_parameters(['username', 'password']);
      let user;
      for (const key of ['email', 'login']) {
        user = table('users').get_one(key, get_param('username'));
        if (user) {
          break;
        }
      }
      if (user && user.verify_passsword(get_param('password'))) {
        signin(user);
        return redirect(get_param('redirect_url', '/my/'));
      }
      response_code(401);
      flash_message('Unknown username/email or invalid password.');
    }
    const params = { token: generate_token('sign_in') };
    return render('auth/signin', params);
  } else if (url_is('/auth/signup')) {
    if (!flag('enable_signup')) {
      return error(200, 'Sign Up is currently disabled.');
    }
    domain('my');
    title(_('Sign Up'));
    if (is_post()) {
      check_parameters(['fullname', 'username', 'email', 'password', 'password_again']);
      const params = {
        name: get_param('fullname'),
        login: get_param('username'),
        email: get_param('email'),
        pass: get_param('password')
      };
      Object.entries(params).forEach(([key, value]) => {
        const error = table('users').validate_field(key, value);
        if (error) {
          form_error(key, error);
        }
      });
      if (get_param('password') !== get_param('password_again')) {
        form_error('password', "Password doesn't match.");
      }
      if (!form_error()) {
        const user = table('users').create(params);
        signin(user);
        flash_message('Sign up successful!');
        return redirect('/my/');
      } else {
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
      let user;
      for (const key of ['email', 'login']) {
        user = table('users').get_one(key, get_param('username'));
        if (user) {
          break;
        }
      }
      if (user) {
        const key = user.generate_activation_key();

        let email =
          _('Someone has asked to reset the password for the following site and username.') +
          "\n\n";
        email += '- ' + _('Site:') + ' ' + absolute_url('/') + "\n";
        email += '- ' + _('Username:') + ' ' + user.login + "\n\n";
        email +=
          _('To reset your password visit the following address, otherwise just ignore this email and nothing will happen.') +
          "\n\n";
        email += absolute_url('/auth/reset-password?key=' + key);

        service('email').send(user.email, 'Reset Password', email);

        return render('auth/forgot-password', { success: true });
      }
      flash_message(_('Unknown username or email.'));
    }
    const params = { token: generate_token('forgot_password') };
    return render('auth/forgot-password', params);
  }

  if (url_is('/auth/reset-password')) {
    domain('my');
    title(_('Reset Password'));
    check_parameters(['key']);
    const user = table('users').get_one('activationkey', get_param('key'));
    if (!user) {
      return render('auth/reset-password', { invalid: true });
    }
    if (is_post()) {
      check_token('reset_password', get_param('reset_password_token'));
      check_parameters(['password_new', 'password_new_confirm']);
      if (!get_param('password_new')) {
        flash_message(_("New password can't be empty."));
      } else if (get_param('password_new') !== get_param('password_new_confirm')) {
        flash_message(_("New password doesn't match confirmation."));
      } else {
        table('users').update(user, { pass: get_param('password_new') });
        return render('auth/reset-password', { success: true });
      }
    }
    const params = { reset_password_token: generate_token('reset_password') };
    return render('auth/reset-password', params);
  } else if (url_is('/auth/signout')) {
    signout();
    return redirect('/');
  }

  return unknown_url();
};
