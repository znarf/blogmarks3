function session_start() {
  const state = global.__amateur_state;
  const current = state && state.current;
  if (!current) {
    return null;
  }
  if (!current.session_id && state.generateSessionId) {
    current.session_id = state.generateSessionId();
  }
  if (!current.session) {
    current.session = {};
  }
  global.SESSION = current.session;
  return current.session_id;
}

module.exports = session_start;
