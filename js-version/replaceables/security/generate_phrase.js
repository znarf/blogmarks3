const nodeCrypto = require('node:crypto');

function generate_phrase(length = 64) {
  const chars = '1234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  let i = 0;
  let phrase = '';
  while (i < length) {
    phrase += chars[nodeCrypto.randomInt(0, chars.length)];
    i += 1;
  }
  return phrase;
}

module.exports = generate_phrase;
