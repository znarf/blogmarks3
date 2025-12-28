function check_method(methods) {
  const request_method = global.replaceable('request_method');
  const list = Array.isArray(methods)
    ? methods.map((value) => String(value).toUpperCase())
    : String(methods || '')
        .split(',')
        .map((value) => value.trim().toUpperCase())
        .filter(Boolean);
  if (!list.includes(request_method())) {
    const http_error = global.replaceable('http_error');
    throw http_error(405, 'Method Not Allowed');
  }
}

module.exports = check_method;
