function signout() {
  SESSION.user_id = null;
  return true;
}

module.exports = signout;
