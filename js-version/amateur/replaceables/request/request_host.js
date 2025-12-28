function request_host() {
  const current = global.__amateur_state && global.__amateur_state.current;
  if (!current || !current.req || !current.req.headers) {
    return 'localhost';
  }
  return current.req.headers.host || 'localhost';
}

module.exports = request_host;
