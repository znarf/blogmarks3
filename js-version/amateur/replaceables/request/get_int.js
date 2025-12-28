function get_int(name, fallback = null) {
  const get_param = global.replaceable('get_param');
  const value = get_param(name, fallback);
  return value === undefined || value === null ? fallback : parseInt(value, 10);
}

module.exports = get_int;
