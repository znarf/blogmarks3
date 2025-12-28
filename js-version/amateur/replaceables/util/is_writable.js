const fs = require('fs');

function is_writable(targetPath) {
  try {
    fs.accessSync(targetPath, fs.constants.W_OK);
    return true;
  } catch (error) {
    return false;
  }
}

module.exports = is_writable;
