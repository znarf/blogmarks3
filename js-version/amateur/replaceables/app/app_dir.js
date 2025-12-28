const path = require('path');

let appDir = './';

function app_dir(value) {
  if (value) {
    appDir = path.resolve(value);
  }
  return appDir;
}

module.exports = app_dir;
