module.exports = function () {
  start_session();

  const user = authenticated_user();
  if (user) {
    init_user_settings(user);
  }

  if (url_is('/') || url_is('/index.php')) {
    redirect('/marks');
  } else if (url_start_with('/my/tools')) {
    moduleAction('tools');
  } else if (url_start_with('/my/profile')) {
    moduleAction('profile');
  } else if (url_start_with('/my/friends')) {
    moduleAction('friends');
  } else if (url_start_with('/my')) {
    moduleAction('my');
  } else if (url_start_with('/auth')) {
    moduleAction('auth');
  } else {
    moduleAction('public');
  }
};
