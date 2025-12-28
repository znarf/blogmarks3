function md5(value) {
  return crypto.createHash('md5').update(String(value)).digest('hex');
}

module.exports = md5;
