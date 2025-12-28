class Container {
  marks(value = undefined) {
    if (value !== undefined) {
      this._marks = value;
      return this._marks;
    }
    return this._marks;
  }

  tags(value = undefined) {
    if (value !== undefined) {
      this._tags = value;
      return this._tags;
    }
    return this._tags;
  }

  users(value = undefined) {
    if (value !== undefined) {
      this._users = value;
      return this._users;
    }
    return this._users;
  }

  __get(name) {
    if (blogmarks.registry.container[name] !== undefined) {
      const value = blogmarks.registry.container[name];
      if (typeof value === 'function') {
        blogmarks.registry.container[name] = value();
        return blogmarks.registry.container[name];
      }
      return value;
    }
  }

  __set(name, value) {
    blogmarks.registry.container[name] = value;
    return blogmarks.registry.container[name];
  }
}

module.exports = new Container();
