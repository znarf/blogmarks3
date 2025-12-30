const path = require('path');

function filename(kind, name) {
  const base = app_dir();
  if (!base) {
    return null;
  }
  if (kind === 'view') {
    return path.join(base, 'views', `${name}.view.js`);
  }
  if (kind === 'layout') {
    return path.join(base, 'layouts', `${name}.layout.js`);
  }
  if (kind === 'partial') {
    return path.join(base, 'partials', `${name}.partial.js`);
  }
  if (kind === 'render') {
    return path.join(base, 'renders', `${name}.render.js`);
  }
  if (kind === 'action') {
    return path.join(base, `${name}.action.js`);
  }
  if (kind === 'module') {
    return path.join(base, 'modules', `${name}.module.js`);
  }
  if (kind === 'helper') {
    return path.join(base, 'helpers', `${name}.helper.js`);
  }
  return null;
}

module.exports = filename;
