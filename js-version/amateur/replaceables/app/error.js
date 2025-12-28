function error(code = 500, message = 'Application Error', trace = '') {
  const response_code = global.replaceable('response_code');
  const view = global.replaceable('view');
  const layout = global.replaceable('layout');
  const default_error = global.replaceable('default_error');
  const finish = global.replaceable('finish');
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
