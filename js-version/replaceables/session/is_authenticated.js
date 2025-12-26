function is_authenticated() {
  return !!blogmarks.authenticated_user();
}

module.exports = is_authenticated;
