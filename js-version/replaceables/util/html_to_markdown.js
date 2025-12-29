const TurndownService = require('turndown');

const turndown = new TurndownService();

function html_to_markdown(value) {
  return turndown.turndown(String(value || ''));
}

module.exports = html_to_markdown;
