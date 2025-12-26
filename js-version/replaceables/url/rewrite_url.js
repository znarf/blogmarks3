function rewrite_url(value) {
  const request_url = blogmarks.request_url();
  let url;
  if (request_url.indexOf('/marks/') === 0) {
    url = request_url.replace('/marks', value);
  } else if (request_url.indexOf('/my/marks/') === 0) {
    url = request_url.replace('/my/marks', value);
  } else if (request_url.indexOf('/my/friends/marks/') === 0) {
    url = request_url.replace('/my/friends/marks', value);
  } else {
    url = value;
  }
  return blogmarks.relative_url(url == request_url ? value : url);
}

module.exports = rewrite_url;
