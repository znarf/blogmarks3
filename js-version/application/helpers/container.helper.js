class Container {
  marks(value = undefined) {
    if (value !== undefined) {
      this._marks = value;
      return this._marks;
    }
    if (typeof this._marks === 'function') {
      this._marks = this._marks();
    }
    return this._marks;
  }

  tags(value = undefined) {
    if (value !== undefined) {
      this._tags = value;
      return this._tags;
    }
    if (typeof this._tags === 'function') {
      this._tags = this._tags();
    }
    return this._tags;
  }

  users(value = undefined) {
    if (value !== undefined) {
      this._users = value;
      return this._users;
    }
    if (typeof this._users === 'function') {
      this._users = this._users();
    }
    return this._users;
  }

}

module.exports = new Container();
