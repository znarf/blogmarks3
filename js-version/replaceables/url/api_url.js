function api_url(path) {
  return 'http://' + request_host() + '/api' + path;
}

module.exports = api_url;
