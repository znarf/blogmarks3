function http_error(code, message = '') {
  const error = new Error(message);
  error.statusCode = code;
  return error;
}

module.exports = http_error;
