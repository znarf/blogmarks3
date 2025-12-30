const Registry = require('./registry');

class blogmarks {
  constructor() {
    return new Proxy(
      {
        registry: Registry,
        config: (key, defaultValue, value) => {
          if (!Registry.config) {
            Registry.config = {};
          }
          if (value !== undefined && value !== null) {
            Registry.config[key] = value;
          }
          if (Registry.config[key] !== undefined) {
            return Registry.config[key];
          }
          return defaultValue;
        },
      },
      {
        get(target, prop) {
          if (prop in target) {
            return target[prop];
          }
          if (prop in global) {
            return global[prop];
          }
          return undefined;
        },
        set(target, prop, value) {
          target[prop] = value;
          return true;
        },
      },
    );
  }
}

module.exports = blogmarks;
