function referer() {
  const request_header = global.replaceable('request_header');
  return String(request_header('referer') || '');
}

module.exports = referer;
