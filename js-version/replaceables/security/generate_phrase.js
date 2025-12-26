function generate_phrase(length = 64) {
  const chars = '1234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  let i = 0;
  let phrase = '';
  while (i < length) {
    phrase += chars[mt_rand(0, chars.length - 1)];
    i += 1;
  }
  return phrase;
}

module.exports = generate_phrase;
