module.exports = function () {
  start_session();

  const user = authenticated_user();
  if (user) {
    init_user_settings(user);
  }

  if (url_is('/') || url_is('/index.php')) {
    redirect('/marks');
  } else if (url_start_with('/my/tools')) {
    module('tools');
  } else if (url_start_with('/my/profile')) {
    module('profile');
  } else if (url_start_with('/my/friends')) {
    module('friends');
  } else if (url_start_with('/my')) {
    module('my');
  } else if (url_start_with('/auth')) {
    module('auth');
  } else {
    module('public');
  }
};
