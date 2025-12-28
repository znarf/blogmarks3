function partial(name, args = {}) {
  const state = global.__amateur_state;
  if (!state.registry.partials) {
    state.registry.partials = {};
  }
  if (args && typeof args === 'function') {
    state.registry.partials[name] = args;
    return state.registry.partials[name];
  }
  if (state.registry.partials[name]) {
    return state.registry.partials[name](args);
  }
  const default_partial = global.replaceable('default_partial');
  let result = default_partial(name, args);
  if (typeof result === 'function') {
    state.registry.partials[name] = result;
    result = result(args);
  }
  return result;
}

module.exports = partial;
