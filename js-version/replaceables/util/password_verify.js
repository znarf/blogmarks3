const bcrypt = require('bcryptjs');

function password_verify(value, hash) {
  try {
    return bcrypt.compareSync(String(value), String(hash));
  } catch (error) {
    return false;
  }
}

module.exports = password_verify;
