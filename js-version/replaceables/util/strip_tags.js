function strip_tags(value) {
  return String(value || '').replace(/<[^>]*>/g, '');
}

module.exports = strip_tags;
