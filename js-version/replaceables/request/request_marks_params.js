function request_marks_params() {
  return {
    offset: blogmarks.get_int('offset', 0),
    limit: blogmarks.get_int('limit', 25),
    order: blogmarks.get_param('order', 'desc'),
    after: blogmarks.get_param('after'),
    before: blogmarks.get_param('before')
  };
}

module.exports = request_marks_params;
