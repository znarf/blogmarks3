function arg(value) {
  const text = global.replaceable('text');
  return text(value);
}

module.exports = arg;
