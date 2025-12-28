function set_param(name, value) {
  const current = global.__amateur_state && global.__amateur_state.current;
  if (current && current.params) {
    current.params[name] = value;
  }
  return value;
}

module.exports = set_param;
