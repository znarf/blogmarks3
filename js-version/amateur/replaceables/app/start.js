function start(callable = null) {
  const action = replaceable('action');
  if (callable) {
    action('start', callable);
  }
  try {
    return action('start');
  } catch (err) {
    const error = replaceable('error');
    if (err && err.statusCode) {
      return error(err.statusCode, err.message, err.stack || '');
    }
    return error(500, err ? err.message : 'Application Error', err && err.stack ? err.stack : '');
  }
}

module.exports = start;
