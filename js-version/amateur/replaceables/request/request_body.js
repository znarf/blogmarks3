function request_body() {
  const current = global.__amateur_state && global.__amateur_state.current;
  if (!current) {
    return '';
  }
  if (current.body !== undefined && current.body !== null) {
    return current.body;
  }
  if (current.req && current.req.body !== undefined) {
    return current.req.body;
  }
  return '';
}

module.exports = request_body;
