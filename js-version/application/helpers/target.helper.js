class Target {
  user(slug = null) {
    if (slug) {
      const user = (blogmarks.registry.target.user = blogmarks.table('users').get_one(
        'login',
        urldecode(slug)
      ));
      if (!user) {
        throw blogmarks.http_error(404, 'User not found');
      }
    }
    if (blogmarks.registry.target.user !== undefined) {
      return blogmarks.registry.target.user;
    }
  }

  tag(slug = null) {
    if (slug) {
      const tag = (blogmarks.registry.target.tag = blogmarks.table('tags').get_one(
        'label',
        urldecode(slug)
      ));
      if (!tag) {
        throw blogmarks.http_error(404, 'Tag not found');
      }
    }
    if (blogmarks.registry.target.tag !== undefined) {
      return blogmarks.registry.target.tag;
    }
  }

  mark(slug = null) {
    if (slug) {
      const mark = (blogmarks.registry.target.mark = blogmarks.table('marks').get_one(
        'id',
        urldecode(slug)
      ));
      if (!mark) {
        throw blogmarks.http_error(404, 'Mark not found');
      }
    }
    if (blogmarks.registry.target.mark !== undefined) {
      return blogmarks.registry.target.mark;
    }
  }
}

module.exports = new Target();
