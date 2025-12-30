function is_write() {
  const request_method = replaceable('request_method');
  return ['POST', 'PUT', 'PATCH', 'DELETE'].includes(request_method());
}

module.exports = is_write;
