function url_start_with(prefix) {
  const current = global.__amateur_state && global.__amateur_state.current;
  return !!(current && current.pathname && current.pathname.startsWith(prefix));
}

module.exports = url_start_with;
