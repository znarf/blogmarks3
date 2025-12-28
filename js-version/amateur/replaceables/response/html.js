function html(body = '') {
  const response_header = global.replaceable('response_header');
  const response_content = global.replaceable('response_content');
  response_header('Content-Type', 'text/html; charset=utf-8');
  return response_content(body);
}

module.exports = html;
