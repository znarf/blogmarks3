function request_files() {
  const state = global.__amateur_state;
  if (!state || !state.current) {
    return {};
  }
  return state.current.files || {};
}

module.exports = request_files;
