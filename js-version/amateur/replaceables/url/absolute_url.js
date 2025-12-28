function absolute_url(url) {
  const request_host = global.replaceable('request_host');
  if (typeof url !== 'string') {
    return url;
  }
  if (url.startsWith('http://') || url.startsWith('https://')) {
    return url;
  }
  return 'http://' + request_host() + url;
}

module.exports = absolute_url;
