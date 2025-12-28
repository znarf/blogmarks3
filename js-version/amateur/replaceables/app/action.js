function action(name, args = {}) {
  const state = global.__amateur_state;
  if (!state.registry.actions) {
    state.registry.actions = {};
  }
  if (args && typeof args === 'function') {
    state.registry.actions[name] = args;
    return args;
  }
  let result;
  if (state.registry.actions[name]) {
    result = state.registry.actions[name](args);
  } else {
    const default_action = global.replaceable('default_action');
    result = default_action(name, args);
  }
  if (typeof result === 'function') {
    state.registry.actions[name] = result;
    result = result(args);
  }
  return result;
}

module.exports = action;
