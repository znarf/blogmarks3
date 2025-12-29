const { format } = require('date-fns');

function format_date(date, pattern) {
  return format(date, pattern);
}

module.exports = format_date;
