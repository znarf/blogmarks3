function title(base = null, arg = null) {
  if (base) {
    blogmarks.registry.title = base;
    if (arg) {
      blogmarks.registry.title += ' <span class="arg">' + arg + '</span>';
    }
  }
  if (blogmarks.registry.title) {
    return blogmarks.registry.title;
  }
}

module.exports = title;
