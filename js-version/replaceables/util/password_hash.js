const bcrypt = require('bcryptjs');

function password_hash(value) {
  return bcrypt.hashSync(String(value), 10);
}

module.exports = password_hash;
