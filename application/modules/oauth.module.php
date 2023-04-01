<?php

$oauth = service('oauth');

if (url_is('/oauth/connect')) {
  $_SESSION['oauth_state'] = $oauth->state();
  return redirect($oauth->authorization_url());
}

else if (url_is('/oauth/callback')) {
  check_parameters(['code', 'state']);

  $code = get_param('code');
  $state = get_param('state');

  # Recommended security checks
  # https://oauth2-client.thephpleague.com/usage/
  if (isset($_SESSION['oauth_state']) && $state !== $_SESSION['oauth_state']) {
    if (isset($_SESSION['oauth_state'])) {
      unset($_SESSION['oauth_state']);
    }
    throw blogmarks::http_error(400, 'Invalid state.');
  }

  # error_log("access_token:$access_token");
  $_SESSION['oauth_access_token'] = $oauth->access_token($code);

  return redirect('/oauth/membership');
}

else if (url_is('/oauth/membership')) {
  $access_token = $_SESSION['oauth_access_token'];

  $authenticated_user = $oauth->authenticated_user($access_token);
  if (empty($authenticated_user)) {
    return redirect('/oauth/connect');
  }

  # error_log(json_encode($authenticated_user));
  $_SESSION['oauth_authenticated_user'] = $authenticated_user;

  $member_of = $authenticated_user['memberOf']['nodes'];
  # If member of blogmarks on Open Collective
  if (!empty($member_of)) {
    $user = table('users')->get_one('email', $authenticated_user['email']);
    # If user found
    if ($user) {
      signin($user);
      return redirect(get_param('redirect_url', '/my/'));
    }
  }

  title(_('Membership'));
  return render('oauth/membership');
}

else {
  return unknown_url();
}
