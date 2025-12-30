function arg(value) {
  const text = replaceable('text');
  return text(value);
}

module.exports = arg;
