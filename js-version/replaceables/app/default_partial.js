function default_partial(name, args = []) {
  const filename = blogmarks.filename('partial', name);
  if (filename) {
    const scoped = args;
    return include(filename, scoped);
  }
  throw blogmarks.http_error(500, `Unknown partial (${name}).`);
}

module.exports = default_partial;
