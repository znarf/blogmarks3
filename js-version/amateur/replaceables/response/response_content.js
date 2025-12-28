function response_content(body = undefined) {
  const state = global.__amateur_state;
  const current = state && state.current;
  if (!current || !current.response) {
    return undefined;
  }
  if (body !== undefined) {
    current.response.body = body;
  }
  return current.response.body;
}

module.exports = response_content;
