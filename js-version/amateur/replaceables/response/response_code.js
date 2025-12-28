function response_code(code = undefined) {
  const state = global.__amateur_state;
  const current = state && state.current;
  if (!current || !current.response) {
    return undefined;
  }
  if (code !== undefined) {
    current.response.code = code;
  }
  return current.response.code;
}

module.exports = response_code;
