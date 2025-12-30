function is_bookmarklet() {
  return get_param('bookmarklet', get_param('mini'));
}

module.exports = is_bookmarklet;
