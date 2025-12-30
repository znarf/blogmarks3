function error(code = 500, message = 'Application Error', trace = '') {
  const response_code = replaceable('response_code');
  const view = replaceable('view');
  const layout = replaceable('layout');
  const default_error = replaceable('default_error');
  const finish = replaceable('finish');
  response_code(code);
  let content = '';
  const views = [String(code), 'error'];
  for (const viewName of views) {
    try {
      const rendered = view(viewName, { code, message, trace });
      if (rendered) {
        content = rendered;
        break;
      }
    } catch (err) {
      continue;
    }
  }
  if (!content) {
    content = default_error(code, message, trace);
  }
  layout('error', content);
  return finish();
}

module.exports = error;
