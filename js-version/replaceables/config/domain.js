function domain(value = null) {
  return config('domain', 'public', value);
}

module.exports = domain;
