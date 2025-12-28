function request_address() {
  const current = global.__amateur_state && global.__amateur_state.current;
  if (!current || !current.req || !current.req.socket) {
    return '';
  }
  return current.req.socket.remoteAddress || '';
}

module.exports = request_address;
