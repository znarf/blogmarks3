class BaseResource {
  constructor(attributes = {}) {
    this.attributes = attributes;
  }

  attribute(name) {
    return this.attributes[name];
  }

  to_objects(rows = []) {
    return rows;
  }
}

module.exports = BaseResource;
