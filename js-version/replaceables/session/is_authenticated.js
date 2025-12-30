function is_authenticated() {
  return !!authenticated_user();
}

module.exports = is_authenticated;
