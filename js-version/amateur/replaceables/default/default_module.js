const fs = require('fs');

function default_module(name) {
  const filename = global.replaceable('filename');
  const file = filename('module', name);
  if (file && fs.existsSync(file)) {
    const exported = require(file);
    return typeof exported === 'function' ? exported : exported;
  }
  throw new Error(`Unknown module (${name}).`);
}

module.exports = default_module;
