function check_token(key, token) {
  if (!SESSION[`csrf_${key}`]) {
    throw blogmarks.http_error(400, 'Missing session token.');
  }
  if (!token) {
    throw blogmarks.http_error(400, 'Missing form token.');
  }
  if (SESSION[`csrf_${key}`] != token) {
    throw blogmarks.http_error(400, 'Invalid token.');
  }
}

module.exports = check_token;
