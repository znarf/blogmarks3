const fs = require('fs');

function default_view(name, args = {}) {
  const filename = global.replaceable('filename');
  const response_content = global.replaceable('response_content');
  const file = filename('view', name);
  if (file && fs.existsSync(file)) {
    const previousFile = global.__FILE__;
    global.__FILE__ = file;
    const exported = require(file);
    const result = typeof exported === 'function' ? exported(args) : exported;
    global.__FILE__ = previousFile;
    return response_content(result);
  }
  throw new Error(`Unknown view (${name}).`);
}

module.exports = default_view;
