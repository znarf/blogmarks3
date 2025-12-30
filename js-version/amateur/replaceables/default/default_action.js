const fs = require('fs');

function default_action(name, args = {}) {
  const filename = replaceable('filename');
  const file = filename('action', name);
  if (file && fs.existsSync(file)) {
    const exported = require(file);
    return typeof exported === 'function' ? exported(args) : exported;
  }
  throw new Error(`Unknown action (${name}).`);
}

module.exports = default_action;
