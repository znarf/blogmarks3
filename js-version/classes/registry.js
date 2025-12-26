const blogmarks = require('./blogmarks');

class registry {
  static services = {};
  static models = {};
  static tables = {};
  static feeds = {};
  static searchs = {};

  static service(name) {
    if (registry.services[name]) {
      return registry.services[name];
    }
    registry.services[name] = blogmarks.instance(`\\blogmarks\\service\\${name}`);
    return registry.services[name];
  }

  static model(name) {
    if (registry.models[name]) {
      return registry.models[name];
    }
    registry.models[name] = blogmarks.instance(`\\blogmarks\\model\\${name}`);
    return registry.models[name];
  }

  static table(name) {
    if (registry.tables[name]) {
      return registry.tables[name];
    }
    registry.tables[name] = blogmarks.instance(`\\blogmarks\\model\\table\\${name}`);
    return registry.tables[name];
  }

  static feed(name) {
    if (registry.feeds[name]) {
      return registry.feeds[name];
    }
    registry.feeds[name] = blogmarks.instance(`\\blogmarks\\model\\feed\\${name}`);
    return registry.feeds[name];
  }

  static search(name) {
    if (registry.searchs[name]) {
      return registry.searchs[name];
    }
    registry.searchs[name] = blogmarks.instance(`\\blogmarks\\model\\search\\${name}`);
    return registry.searchs[name];
  }
}

module.exports = registry;
