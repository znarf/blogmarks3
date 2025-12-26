function check_authenticated() {
  if (!blogmarks.is_authenticated()) {
    blogmarks.response_code(401);
    return blogmarks.render('auth/signin', { token: blogmarks.generate_token('sign_in') });
  }
}

module.exports = check_authenticated;
