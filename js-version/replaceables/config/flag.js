function flag(name, value = null) {
  const key = `flag_${name}`;
  if (value !== null && value !== undefined) {
    return blogmarks.config(key, null, value);
  }
  return blogmarks.config(key, null);
}

module.exports = flag;
