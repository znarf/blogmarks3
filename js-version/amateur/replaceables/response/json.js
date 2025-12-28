function json(data = null) {
  const response_header = global.replaceable('response_header');
  const response_content = global.replaceable('response_content');
  response_header('Content-Type', 'application/json; charset=utf-8');
  return response_content(JSON.stringify(data));
}

module.exports = json;
