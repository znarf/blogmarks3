function generate_token(key) {
  SESSION[`csrf_${key}`] = blogmarks.generate_phrase();
  return SESSION[`csrf_${key}`];
}

module.exports = generate_token;
