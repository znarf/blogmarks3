function start_session() {
  if (!session_id()) {
    session_start();
  }
}

module.exports = start_session;
