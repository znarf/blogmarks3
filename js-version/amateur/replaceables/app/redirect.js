function redirect(pathname) {
  const response_code = global.replaceable('response_code');
  const response_header = global.replaceable('response_header');
  const response_content = global.replaceable('response_content');
  response_code(302);
  response_header('Location', pathname);
  response_content('');
  return '';
}

module.exports = redirect;
