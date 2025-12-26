function partial(name, args = []) {
  if (!blogmarks.registry.partials) {
    blogmarks.registry.partials = {};
  }
  if (args && is_callable(args)) {
    blogmarks.registry.partials[name] = args;
    return blogmarks.registry.partials[name];
  }
  if (blogmarks.registry.partials[name]) {
    const stored = blogmarks.registry.partials[name];
    return stored(args);
  }
  const default_partial = blogmarks.replaceable('default_partial');
  let result = default_partial(name, args);
  if (is_callable(result)) {
    const stored = (blogmarks.registry.partials[name] = result);
    result = stored(args);
  }
  return result;
}

module.exports = partial;
