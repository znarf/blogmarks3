const Replaceable = require('../../classes/replaceable');

function replaceable(name, fn) {
  if (typeof fn === 'function') {
    return Replaceable.set(name, fn);
  }
  return Replaceable.get(name);
}

module.exports = replaceable;
