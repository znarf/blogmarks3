function include(filePath, args = {}) {
  const previousFile = global.__FILE__;
  global.__FILE__ = filePath;
  const exported = require(filePath);
  let result;
  if (typeof exported === 'function') {
    result = exported(args);
  } else {
    result = exported;
  }
  global.__FILE__ = previousFile;
  return result;
}

module.exports = include;
