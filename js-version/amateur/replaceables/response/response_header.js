function response_header(name = null, value = null) {
  const state = global.__amateur_state;
  const current = state && state.current;
  if (!current || !current.response) {
    return {};
  }
  if (name) {
    if (value !== null && value !== undefined) {
      current.response.headers[name] = value;
    } else {
      delete current.response.headers[name];
    }
  }
  return current.response.headers || {};
}

module.exports = response_header;
