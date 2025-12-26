function signin(user) {
  SESSION.user_id = user.id;
  return true;
}

module.exports = signin;
