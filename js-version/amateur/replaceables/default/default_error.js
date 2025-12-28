function default_error(code = 500, message = 'Application Error', trace = '') {
  let content = `<h2>${message}</h2>`;
  if (trace) {
    content += `<pre>${trace}</pre>`;
  }
  return content;
}

module.exports = default_error;
