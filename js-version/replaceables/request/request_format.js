function request_format(value = null) {
  if (value) {
    blogmarks.registry.request_format = value;
  }
  if (blogmarks.registry.request_format) {
    return blogmarks.registry.request_format;
  }
  const format = get_param('format');
  if (format && ['json', 'rss', 'atom'].includes(format)) {
    blogmarks.registry.request_format = format;
    return blogmarks.registry.request_format;
  }
  const accept = request_header('Accept');
  const mime = {
    'application/json': 'json',
    'application/rss+xml': 'rss',
    'application/atom+xml': 'atom',
  };
  if (accept && mime[accept]) {
    blogmarks.registry.request_format = mime[accept];
    return blogmarks.registry.request_format;
  }
  blogmarks.registry.request_format = default_format();
  return blogmarks.registry.request_format;
}

module.exports = request_format;
