function get_bool(name, fallback = false) {
  const get_param = global.replaceable('get_param');
  const value = get_param(name);
  if (value === undefined || value === null) {
    return fallback;
  }
  return value === true || value === '1' || value === 'true';
}

module.exports = get_bool;
