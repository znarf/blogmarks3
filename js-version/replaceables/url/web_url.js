function web_url(path) {
  return 'http://' + blogmarks.request_host() + path;
}

module.exports = web_url;
