class Container {
  marks(value = undefined) {
    if (value !== undefined) {
      this.marks = value;
      return this.marks;
    }
    return this.marks;
  }

  tags(value = undefined) {
    if (value !== undefined) {
      this.tags = value;
      return this.tags;
    }
    return this.tags;
  }

  users(value = undefined) {
    if (value !== undefined) {
      this.users = value;
      return this.users;
    }
    return this.users;
  }

  __get(name) {
    if (blogmarks.registry.container[name] !== undefined) {
      const value = blogmarks.registry.container[name];
      if (is_callable(value)) {
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
