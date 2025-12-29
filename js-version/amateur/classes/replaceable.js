const fs = require('fs');
const path = require('path');

const AmateurException = require('./exception');

class Replaceable {
  static index = {};
  static replaceables = {};
  static expose = false;

  static load_replaceables(dir) {
    const entries = fs.readdirSync(dir, { withFileTypes: true });
    entries.forEach((entry) => {
      const fullPath = path.join(dir, entry.name);
      if (entry.isDirectory()) {
        Replaceable.load_replaceables(fullPath);
        return;
      }
      if (entry.isFile() && entry.name.endsWith('.js')) {
        const name = path.basename(entry.name, '.js');
        Replaceable.index[name] = { filename: fullPath };
      }
    });
  }

  static load_replaceable(name, filename) {
    const exported = require(filename);
    if (typeof exported !== 'function') {
      return Replaceable.set(name, exported);
    }
    return Replaceable.set(name, exported);
  }

  static get(name, throwError = false) {
    if (Array.isArray(name)) {
      return name.map((item) => Replaceable.get(item, throwError));
    }
    if (Replaceable.replaceables[name]) {
      return Replaceable.replaceables[name];
    }
    if (Replaceable.index[name]) {
      return Replaceable.load_replaceable(name, Replaceable.index[name].filename);
    }
    if (throwError) {
      throw new AmateurException(`Unknown replaceable (${name}).`, 500);
    }
    return undefined;
  }

  static set(name, replaceable) {
    if (Replaceable.expose) {
      Replaceable.create_global_function(name);
    }
    Replaceable.replaceables[name] = replaceable;
    return replaceable;
  }

  static call(name, args) {
    const callable = Replaceable.get(name, true);
    return callable(...args);
  }

  static create_global_function(name) {
    if (global[name]) {
      return;
    }
    global[name] = (...args) => Replaceable.call(name, args);
  }

  static expose_replaceables() {
    Replaceable.expose = true;
    const names = Object.keys(Replaceable.replaceables);
    const indexed = Object.keys(Replaceable.index);
    [...new Set([...names, ...indexed])].forEach((name) => {
      Replaceable.create_global_function(name);
    });
  }
}

module.exports = Replaceable;
