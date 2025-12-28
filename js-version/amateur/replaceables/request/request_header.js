function request_header(name) {
  const current = global.__amateur_state && global.__amateur_state.current;
  if (!current || !current.req || !current.req.headers) {
    return undefined;
  }
  return current.req.headers[String(name).toLowerCase()];
}

module.exports = request_header;
