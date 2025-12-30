function is_get() {
  const request_method = replaceable('request_method');
  return request_method() === 'GET';
}

module.exports = is_get;
