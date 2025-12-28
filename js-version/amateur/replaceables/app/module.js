function moduleAction(name, callable = null) {
  const state = global.__amateur_state;
  if (!state.registry.modules) {
    state.registry.modules = {};
  }
  if (callable && typeof callable === 'function') {
    state.registry.modules[name] = callable;
    return callable;
  }
  if (state.registry.modules[name]) {
    return state.registry.modules[name]();
  }
  const default_module = global.replaceable('default_module');
  let result = default_module(name);
  if (typeof result === 'function') {
    state.registry.modules[name] = result;
    result = result();
  }
  return result;
}

module.exports = moduleAction;
