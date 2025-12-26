function table(name) {
  if (Array.isArray(name)) {
    return name.map(table);
  }
  return registry.table(name);
}

module.exports = table;
