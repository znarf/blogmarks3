function request_param(name, fallback = null) {
  const get_param = global.replaceable('get_param');
  return get_param(name, fallback);
}

module.exports = request_param;
