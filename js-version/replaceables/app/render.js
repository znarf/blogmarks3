const fs = require('fs');

function render(view, args = [], layout = 'default') {
  const filename = blogmarks.filename('render', view);
  if (filename && fs.existsSync(filename)) {
    const scoped = args;
    const result = include(filename, scoped);
    if (result !== undefined) {
      return blogmarks.response_content(result);
    }
  } else {
    const content = blogmarks.view(view, args);
    const layoutOutput = blogmarks.layout(layout);
    return blogmarks.response_content(layoutOutput || content || '');
  }
  if (blogmarks.registry.layout_output) {
    return blogmarks.response_content(blogmarks.registry.layout_output);
  }
  blogmarks.finish();
}

module.exports = render;
