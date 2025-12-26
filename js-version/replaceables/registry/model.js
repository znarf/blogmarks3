function model(name, value = null) {
  if (Array.isArray(name)) {
    return name.map(model);
  }
  if (name === 'marks' || name === 'tags') {
    return registry.model(name);
  }
  return blogmarks.table(name);
}

module.exports = model;
