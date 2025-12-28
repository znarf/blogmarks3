function url_match(pattern) {
  const current = global.__amateur_state && global.__amateur_state.current;
  if (!current) {
    return null;
  }
  const escaped = String(pattern).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  const regex = new RegExp('^' + escaped.replace('\\*', '(.+)') + '$');
  const match = current.pathname.match(regex);
  return match || null;
}

module.exports = url_match;
