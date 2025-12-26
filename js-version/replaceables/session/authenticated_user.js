function authenticated_user(value = null) {
  if (value) {
    blogmarks.registry.user = value;
  }
  if (blogmarks.registry.user) {
    return blogmarks.registry.user;
  }
  if (SESSION.user_id !== undefined) {
    blogmarks.registry.user = blogmarks.table('users').get(SESSION.user_id);
    return blogmarks.registry.user;
  }
}

module.exports = authenticated_user;
