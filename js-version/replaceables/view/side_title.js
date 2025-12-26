function side_title(base = null, arg = null) {
  if (!blogmarks.registry.side_title) {
    blogmarks.registry.side_title = '<strong>Public</strong> Tags';
  }
  if (base) {
    blogmarks.registry.side_title = blogmarks.strong(base);
    if (arg) {
      blogmarks.registry.side_title += ' ' + arg;
    }
  }
  return blogmarks.registry.side_title;
}

module.exports = side_title;
