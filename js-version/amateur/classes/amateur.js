const path = require('path');

class Amateur {
  static instance(className) {
    const cleaned = String(className).replace(/^\\+/, '');
    const parts = cleaned.split('\\').map((part) => part.toLowerCase());
    let fileParts = parts;
    if (fileParts[0] === 'blogmarks') {
      fileParts = fileParts.slice(1);
    }
    const filePath = path.join(__dirname, '..', '..', 'classes', ...fileParts) + '.js';
    const Exported = require(filePath);
    if (typeof Exported === 'function') {
      const instance = new Exported();
      const attachUse = (value, name) => {
        if (typeof value !== 'function') {
          return;
        }
        const descriptor = Object.getOwnPropertyDescriptor(value, '__use');
        if (descriptor && descriptor.writable === false) {
          return;
        }
        try {
          Object.defineProperty(value, '__use', {
            value: (...args) => instance[name](...args),
            writable: true,
            configurable: true
          });
        } catch (error) {
          return;
        }
      };
      Object.getOwnPropertyNames(instance).forEach((name) => {
        attachUse(instance[name], name);
      });
      const proto = Object.getPrototypeOf(instance);
      Object.getOwnPropertyNames(proto).forEach((name) => {
        if (name === 'constructor') {
          return;
        }
        if (typeof instance[name] === 'function') {
          instance[name] = instance[name].bind(instance);
        }
        attachUse(instance[name], name);
      });
      return instance;
    }
    return Exported;
  }

  static web_url() {
    throw new Error('web_url is app-level, use the replaceable instead.');
  }

  static generate_phrase(length = 64) {
    if (global.generate_phrase) {
      return global.generate_phrase(length);
    }
    return '';
  }
}

module.exports = Amateur;
