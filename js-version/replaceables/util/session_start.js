function session_start() {
  const state = global.__amateur_state;
  const current = state && state.current;
  if (!current) {
    return null;
  }
  if (!current.session_id) {
    current.session_id = generate_session_id();
  }
  if (!current.session) {
    current.session = {};
  }
  global.SESSION = current.session;
  return current.session_id;
}

module.exports = session_start;
