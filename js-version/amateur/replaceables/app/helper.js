function helper(name, value = null) {
  const state = global.__amateur_state;
  if (!state.registry.helpers) {
    state.registry.helpers = {};
  }
  if (Array.isArray(name)) {
    return name.map((entry) => helper(entry));
  }
  if (value && typeof value === 'object') {
    state.registry.helpers[name] = value;
    return value;
  }
  if (Object.prototype.hasOwnProperty.call(state.registry.helpers, name)) {
    let stored = state.registry.helpers[name];
    if (typeof stored === 'function') {
      stored = state.registry.helpers[name] = stored();
    }
    return stored;
  }
  const default_helper = global.replaceable('default_helper');
  let loaded = default_helper(name);
  if (typeof loaded === 'function') {
    loaded = loaded();
  }
  state.registry.helpers[name] = loaded;
  return loaded;
}

module.exports = helper;
