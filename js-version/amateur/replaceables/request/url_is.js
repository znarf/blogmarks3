function url_is(value) {
  const current = global.__amateur_state && global.__amateur_state.current;
  return !!(current && current.pathname === value);
}

module.exports = url_is;
