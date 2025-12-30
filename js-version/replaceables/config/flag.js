function flag(name, value = null) {
  const key = `flag_${name}`;
  if (value !== null && value !== undefined) {
    return config(key, null, value);
  }
  return config(key, null);
}

module.exports = flag;
