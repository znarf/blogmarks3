function get_param(name, fallback = null) {
  const current = global.__amateur_state && global.__amateur_state.current;
  if (!current || !current.params) {
    return fallback;
  }
  const value = current.params[name];
  return value !== undefined ? value : fallback;
}

module.exports = get_param;
