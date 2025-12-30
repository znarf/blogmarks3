function render(name, args = {}, layoutName = 'default') {
  const view = replaceable('view');
  const layout = replaceable('layout');
  const response_content = replaceable('response_content');
  const content = view(name, args);
  const output = layout(layoutName);
  return response_content(output || content || '');
}

module.exports = render;
