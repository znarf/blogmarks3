function table(name) {
  if (Array.isArray(name)) {
    return name.map(table);
  }
  return blogmarks.registry.table(name);
}

module.exports = table;
