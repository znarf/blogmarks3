function request_marks_params() {
  return {
    offset: get_int('offset', 0),
    limit: get_int('limit', 25),
    order: get_param('order', 'desc'),
    after: get_param('after', '-inf'),
    before: get_param('before', '+inf'),
  };
}

module.exports = request_marks_params;
