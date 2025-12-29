const Showdown = require('showdown');

const converter = new Showdown.Converter();

function markdown_to_html(value) {
  return converter.makeHtml(String(value || ''));
}

module.exports = markdown_to_html;
