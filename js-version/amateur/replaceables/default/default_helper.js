const fs = require('fs');

function default_helper(name) {
  const filename = global.replaceable('filename');
  const file = filename('helper', name);
  if (file && fs.existsSync(file)) {
    const exported = require(file);
    return typeof exported === 'function' ? exported : exported;
  }
  throw new Error(`Unknown helper (${name}).`);
}

module.exports = default_helper;
