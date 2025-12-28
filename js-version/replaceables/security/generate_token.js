function generate_token(key) {
  SESSION[`csrf_${key}`] = generate_phrase();
  return SESSION[`csrf_${key}`];
}

module.exports = generate_token;
