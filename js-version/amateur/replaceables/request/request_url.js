function request_url() {
  const current = global.__amateur_state && global.__amateur_state.current;
  return current ? current.pathname + current.search : '';
}

module.exports = request_url;
