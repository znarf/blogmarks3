class Replaceable {
  constructor(registry) {
    this.registry = registry;
  }

  set(name, fn) {
    this.registry.replaceables[name] = fn;
    return fn;
  }

  get(name) {
    return this.registry.replaceables[name];
  }
}

module.exports = Replaceable;
