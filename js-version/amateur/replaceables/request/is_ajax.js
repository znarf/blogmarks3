function is_ajax() {
  const request_header = global.replaceable('request_header');
  return String(request_header('x-requested-with') || '').toLowerCase() === 'xmlhttprequest';
}

module.exports = is_ajax;
