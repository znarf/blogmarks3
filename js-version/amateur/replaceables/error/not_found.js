function not_found(message = 'Not Found') {
  const error = global.replaceable('error');
  return error(404, message);
}

module.exports = not_found;
