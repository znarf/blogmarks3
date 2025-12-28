function view(name, args = {}) {
  const state = global.__amateur_state;
  if (!state.registry.views) {
    state.registry.views = {};
  }
  if (args && typeof args === 'function') {
    state.registry.views[name] = args;
    return args;
  }
  const response_content = global.replaceable('response_content');
  if (state.registry.views[name]) {
    const output = state.registry.views[name](args || {});
    return response_content(output);
  }
  const default_view = global.replaceable('default_view');
  return default_view(name, args);
}

module.exports = view;
