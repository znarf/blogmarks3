const registry = require('../registry');

class base {
  constructor() {
    this.table = registry.table.bind(registry);
    this.feed = registry.feed.bind(registry);
    this.search = registry.search.bind(registry);
    this.service = registry.service.bind(registry);
    this.model = registry.model.bind(registry);
  }
}

module.exports = base;
