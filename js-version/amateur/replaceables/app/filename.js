const path = require('path');

function filename(kind, name) {
  const state = global.__amateur_state;
  if (!state || !state.paths) {
    return null;
  }
  if (kind === 'view') {
    return path.join(state.paths.views, `${name}.view.js`);
  }
  if (kind === 'layout') {
    return path.join(state.paths.layouts, `${name}.layout.js`);
  }
  if (kind === 'partial') {
    return path.join(state.paths.partials, `${name}.partial.js`);
  }
  if (kind === 'render') {
    return path.join(state.paths.renders, `${name}.render.js`);
  }
  if (kind === 'action') {
    return path.join(state.paths.actions, `${name}.action.js`);
  }
  if (kind === 'module') {
    return path.join(state.paths.modules, `${name}.module.js`);
  }
  if (kind === 'helper') {
    return path.join(state.paths.helpers, `${name}.helper.js`);
  }
  return null;
}

module.exports = filename;
