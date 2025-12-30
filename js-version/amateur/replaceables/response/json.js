function json(data = null) {
  const response_header = replaceable('response_header');
  const response_content = replaceable('response_content');
  response_header('Content-Type', 'application/json; charset=utf-8');
  return response_content(JSON.stringify(data));
}

module.exports = json;
