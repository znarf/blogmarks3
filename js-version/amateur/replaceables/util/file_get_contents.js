const fs = require('fs');

function file_get_contents(filename) {
  return fs.readFileSync(String(filename), 'utf8');
}

module.exports = file_get_contents;
