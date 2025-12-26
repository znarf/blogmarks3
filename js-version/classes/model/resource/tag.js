const resource = require('../resource');

class tag extends resource {
  toString() {
    return this.label;
  }
}

module.exports = tag;
