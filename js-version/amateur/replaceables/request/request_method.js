function request_method() {
  const current = global.__amateur_state && global.__amateur_state.current;
  return current ? current.method : '';
}

module.exports = request_method;
