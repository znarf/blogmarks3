function generate_session_id() {
  return Math.random().toString(36).slice(2) + Date.now().toString(36);
}

module.exports = generate_session_id;
