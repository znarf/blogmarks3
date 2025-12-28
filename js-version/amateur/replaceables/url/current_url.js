function current_url() {
  const request_url = global.replaceable('request_url');
  const absolute_url = global.replaceable('absolute_url');
  return absolute_url(request_url());
}

module.exports = current_url;
