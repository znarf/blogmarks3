function flash_message(message = null) {
  if (message) {
    SESSION.message = message;
    return SESSION.message;
  }
  if (SESSION.message !== undefined) {
    const stored = SESSION.message;
    delete SESSION.message;
    return stored;
  }
}

module.exports = flash_message;
