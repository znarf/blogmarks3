const resource = require('../resource');

class tag extends resource {
  constructor(attributes = {}) {
    super(attributes);
    Object.assign(this, attributes);
  }

  toString() {
    return this.label;
  }
}

module.exports = tag;
