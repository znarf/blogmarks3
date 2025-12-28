class ModelException extends Error {
  constructor(message = 'Model Error', code = 500) {
    super(message);
    this.code = code;
  }
}

module.exports = ModelException;
