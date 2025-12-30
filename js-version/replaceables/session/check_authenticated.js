function check_authenticated() {
  if (!is_authenticated()) {
    response_code(401);
    return render('auth/signin', { token: generate_token('sign_in') });
  }
}

module.exports = check_authenticated;
