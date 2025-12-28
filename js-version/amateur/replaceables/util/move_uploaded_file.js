const fs = require('fs');

function move_uploaded_file(source, destination) {
  try {
    fs.copyFileSync(source, destination);
    return true;
  } catch (error) {
    return false;
  }
}

module.exports = move_uploaded_file;
