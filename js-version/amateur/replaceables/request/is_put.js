function is_put() {
  const request_method = replaceable('request_method');
  return request_method() === 'PUT';
}

module.exports = is_put;
