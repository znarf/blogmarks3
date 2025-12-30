function is_delete() {
  const request_method = replaceable('request_method');
  return request_method() === 'DELETE';
}

module.exports = is_delete;
