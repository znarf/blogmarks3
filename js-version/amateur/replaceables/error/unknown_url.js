function unknown_url() {
  const response_code = replaceable('response_code');
  response_code(404);
  return '';
}

module.exports = unknown_url;
