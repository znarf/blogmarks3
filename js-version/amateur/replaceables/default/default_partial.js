const fs = require('fs');

function default_partial(name, args = {}) {
  const filename = replaceable('filename');
  const file = filename('partial', name);
  if (file && fs.existsSync(file)) {
    const exported = require(file);
    return typeof exported === 'function' ? exported(args) : exported;
  }
  throw new Error(`Unknown partial (${name}).`);
}

module.exports = default_partial;
