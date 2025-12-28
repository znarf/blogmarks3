function layout(name, args = {}) {
  const state = global.__amateur_state;
  if (!state.registry.layouts) {
    state.registry.layouts = {};
  }
  if (args && typeof args === 'function') {
    state.registry.layouts[name] = args;
    return args;
  }
  const response_content = global.replaceable('response_content');
  let payload = args;
  if (typeof payload === 'string') {
    payload = { content: payload };
  }
  if (!payload || !payload.content) {
    const current = state.current;
    payload = { ...(payload || {}), content: current && current.response ? current.response.body : '' };
  }
  const contentValue = payload.content;
  let output;
  if (state.registry.layouts[name]) {
    output = state.registry.layouts[name](contentValue);
  } else {
    const default_layout = global.replaceable('default_layout');
    output = default_layout(name, payload);
  }
  return response_content(output);
}

module.exports = layout;
