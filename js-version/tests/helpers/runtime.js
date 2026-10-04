const path = require('path');
const amateur = require('../../amateur/amateur');
const Replaceable = require('../../amateur/classes/replaceable');
const Blogmarks = require('../../classes/blogmarks');

function setupRuntime() {
  if (!process.env.SESSION_SECRET) {
    process.env.SESSION_SECRET = 'test-secret';
  }
  const root = path.join(__dirname, '..', '..');
  global.amateur = amateur;
  amateur.initGlobals();
  Replaceable.load_replaceables(path.join(root, 'replaceables'));
  Replaceable.get('expose_replaceables')();
  app_dir(path.join(root, 'application'));
  global.blogmarks = new Blogmarks();
  return amateur;
}

module.exports = {
  setupRuntime,
};
