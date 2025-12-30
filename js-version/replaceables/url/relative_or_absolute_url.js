function relative_or_absolute_url(url) {
  return request_format() == 'html' ? relative_url(url) : absolute_url(url);
}

module.exports = relative_or_absolute_url;
