function domain(value = null) {
  return blogmarks.config('domain', 'public', value);
}

module.exports = domain;
