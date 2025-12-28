function is_post() {
  const request_method = global.replaceable('request_method');
  return request_method() === 'POST';
}

module.exports = is_post;
