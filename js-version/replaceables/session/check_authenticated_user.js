function check_authenticated_user(check_user) {
  if (!is_authenticated_user(check_user)) {
    throw http_error(403, 'Forbidden.');
  }
}

module.exports = check_authenticated_user;
