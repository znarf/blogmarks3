function check_parameters(names = []) {
  names.forEach((name) => {
    const value = get_param(name);
    if (value === undefined || value === null) {
      throw http_error(400, `Missing parameter (${name}).`);
    }
  });
  return true;
}

module.exports = check_parameters;
