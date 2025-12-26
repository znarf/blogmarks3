function is_bookmarklet() {
  return blogmarks.get_param('bookmarklet', blogmarks.get_param('mini'));
}

module.exports = is_bookmarklet;
