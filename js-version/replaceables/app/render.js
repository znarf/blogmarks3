const fs = require('fs');

function render(viewName, args = {}, layoutName = 'default') {
  const filename = replaceable('filename')('render', viewName);
  if (filename && fs.existsSync(filename)) {
    replaceable('include')(filename, args);
  } else {
    replaceable('view')(viewName, args);
    replaceable('layout')(layoutName);
  }
  replaceable('finish')();
  return replaceable('response_content')();
}

module.exports = render;
