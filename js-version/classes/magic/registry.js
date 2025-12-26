const registry = require('../registry');

const magicRegistry = {
  service(name) {
    return registry.service(name);
  },
  model(name) {
    return registry.model(name);
  },
  table(name) {
    return registry.table(name);
  },
  feed(name) {
    return registry.feed(name);
  },
  search(name) {
    return registry.search(name);
  }
};

module.exports = magicRegistry;
