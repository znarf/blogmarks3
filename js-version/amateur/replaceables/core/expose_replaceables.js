function expose_replaceables() {
  const state = global.__amateur_state;
  if (!state || !state.registry) {
    return;
  }
  state.registry.expose = true;
  Object.keys(state.registry.replaceables).forEach((name) => {
    if (name === 'replaceable' || name === 'amateur') {
      return;
    }
    global[name] = (...args) => state.registry.replaceables[name](...args);
  });
}

module.exports = expose_replaceables;
