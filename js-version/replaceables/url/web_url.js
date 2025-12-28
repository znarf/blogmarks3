function web_url(path) {
  return 'http://' + request_host() + path;
}

module.exports = web_url;
