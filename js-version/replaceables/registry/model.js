function model(name) {
  if (Array.isArray(name)) {
    return name.map(model);
  }
  if (name === 'marks' || name === 'tags') {
    return blogmarks.registry.model(name);
  }
  return table(name);
}

module.exports = model;
