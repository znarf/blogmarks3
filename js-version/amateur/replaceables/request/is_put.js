function is_put() {
  const request_method = global.replaceable('request_method');
  return request_method() === 'PUT';
}

module.exports = is_put;
