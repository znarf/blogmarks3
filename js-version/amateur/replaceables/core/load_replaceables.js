const fs = require('fs');
const path = require('path');

function load_replaceables(dir) {
  const replaceable = global.replaceable;
  const entries = fs.readdirSync(dir, { withFileTypes: true });
  entries.forEach((entry) => {
    const fullPath = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      load_replaceables(fullPath);
      return;
    }
    if (entry.isFile() && entry.name.endsWith('.js')) {
      const name = path.basename(entry.name, '.js');
      const exported = require(fullPath);
      if (typeof exported === 'function') {
        replaceable(name, exported);
      }
    }
  });
}

module.exports = load_replaceables;
