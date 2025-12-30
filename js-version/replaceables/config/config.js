function config(key, defaultValue = null, value) {
  if (!global.blogmarks || !global.blogmarks.registry) {
    return defaultValue;
  }
  if (!global.blogmarks.registry.config) {
    global.blogmarks.registry.config = {};
  }
  if (value !== undefined && value !== null) {
    global.blogmarks.registry.config[key] = value;
  }
  if (global.blogmarks.registry.config[key] !== undefined) {
    return global.blogmarks.registry.config[key];
  }
  return defaultValue;
}

module.exports = config;
