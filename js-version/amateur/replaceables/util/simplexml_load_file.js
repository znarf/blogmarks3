const fs = require('fs');

function simplexml_load_file(filename) {
  try {
    const contents = fs.readFileSync(String(filename), 'utf8');
    return simplexml_load_string(contents);
  } catch (error) {
    return { entry: [] };
  }
}

module.exports = simplexml_load_file;
