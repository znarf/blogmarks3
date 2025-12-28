const fs = require('fs');

function default_layout(name, args = {}) {
  const filename = global.replaceable('filename');
  const response_content = global.replaceable('response_content');
  const layout = global.replaceable('layout');
  const file = filename('layout', name);
  if (file && fs.existsSync(file)) {
    const exported = require(file);
    const contentValue = args && Object.prototype.hasOwnProperty.call(args, 'content') ? args.content : args;
    return typeof exported === 'function' ? exported(contentValue) : exported;
  }
  if (name === 'none' || name === 'default') {
    return response_content(args.content);
  }
  return layout('default', args);
}

module.exports = default_layout;
