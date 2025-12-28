function has_param(name) {
  const current = global.__amateur_state && global.__amateur_state.current;
  return !!(current && current.params && Object.prototype.hasOwnProperty.call(current.params, name));
}

module.exports = has_param;
