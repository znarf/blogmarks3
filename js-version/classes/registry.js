const path = require('path');

class registry {
  static services = {};
  static models = {};
  static tables = {};
  static feeds = {};
  static searchs = {};

  static target = {};
  static container = {};

  static _loadInstance(filePath) {
    const Exported = require(filePath);
    if (typeof Exported !== 'function') {
      return Exported;
    }
    return new Exported();
  }

  static _resolvePath(parts) {
    return path.join(__dirname, ...parts) + '.js';
  }

  static _get(cache, parts, name) {
    if (cache[name]) {
      return cache[name];
    }
    cache[name] = registry._loadInstance(registry._resolvePath(parts));
    return cache[name];
  }

  static service(name) {
    return registry._get(registry.services, ['service', name], name);
  }

  static model(name) {
    return registry._get(registry.models, ['model', name], name);
  }

  static table(name) {
    return registry._get(registry.tables, ['model', 'table', name], name);
  }

  static feed(name) {
    return registry._get(registry.feeds, ['model', 'feed', name], name);
  }

  static search(name) {
    return registry._get(registry.searchs, ['model', 'search', name], name);
  }

  // Clears per-request state to avoid cross-request leakage.
  static reset() {
    registry.content = '';
    registry.layout_output = '';
    registry.side_title = null;
    registry.config = {};
    registry.container = {};
    registry.target = {};
    if (amateur.registry?.helpers?.sidebar) {
      amateur.registry.helpers.sidebar.empty();
    }
  }
}

module.exports = registry;
