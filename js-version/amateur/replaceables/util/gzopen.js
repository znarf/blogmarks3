const fs = require('fs');
const zlib = require('zlib');

function gzopen(filename) {
  const buffer = fs.readFileSync(String(filename));
  const data = zlib.gunzipSync(buffer).toString('utf8');
  return { data, offset: 0 };
}

module.exports = gzopen;
