function render(name, args = {}, layoutName = 'default') {
  const view = global.replaceable('view');
  const layout = global.replaceable('layout');
  const response_content = global.replaceable('response_content');
  const content = view(name, args);
  const output = layout(layoutName);
  return response_content(output || content || '');
}

module.exports = render;
