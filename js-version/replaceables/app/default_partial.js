function default_partial(name, args = []) {
  const file = filename('partial', name);
  if (file) {
    return include(file, args);
  }
  throw http_error(500, `Unknown partial (${name}).`);
}

module.exports = default_partial;
