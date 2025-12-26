function is_authenticated_user(check_user) {
  const user = blogmarks.authenticated_user();
  if (user) {
    if (user.id == check_user.id) {
      return true;
    }
  }
  return false;
}

module.exports = is_authenticated_user;
