function render(view, args = [], layout = 'default') {
  const filename = blogmarks.filename('render', view);
  if (filename) {
    const scoped = args;
    include(filename, scoped);
  } else {
    blogmarks.view(view, args);
    blogmarks.layout(layout);
  }
  blogmarks.finish();
}

module.exports = render;
