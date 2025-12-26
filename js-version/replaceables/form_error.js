function form_error(name = null, message = null) {
  if (!blogmarks.registry.errors) {
    blogmarks.registry.errors = [];
  }
  if (name && message) {
    blogmarks.registry.errors[name] = message;
    return blogmarks.registry.errors[name];
  }
  if (name) {
    return blogmarks.registry.errors[name] ? blogmarks.registry.errors[name] : [];
  }
  return blogmarks.registry.errors;
}

module.exports = form_error;
