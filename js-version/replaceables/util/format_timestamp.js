const { getUnixTime } = require('date-fns');

function format_timestamp(date) {
  return getUnixTime(date);
}

module.exports = format_timestamp;
