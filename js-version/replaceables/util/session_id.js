function session_id() {
  const state = global.__amateur_state;
  const current = state && state.current;
  return current ? current.session_id : null;
}

module.exports = session_id;
