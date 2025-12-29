class Cache {
  constructor() {
    this.store = new Map();
  }

  get(key) {
    return this.store.get(key);
  }

  set(key, value) {
    this.store.set(key, value);
    return value;
  }

  preload() {
    return true;
  }

  loaded(key) {
    return this.store.has(key);
  }
}

module.exports = Cache;
