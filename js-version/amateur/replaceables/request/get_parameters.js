function get_parameters(names = []) {
  const get_param = global.replaceable('get_param');
  const params = {};
  names.forEach((name) => {
    params[name] = get_param(name);
  });
  return params;
}

module.exports = get_parameters;
