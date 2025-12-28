class AmateurException extends Error {
  constructor(message = 'Application Error', code = 500) {
    super(message);
    this.code = code;
  }

  getCode() {
    return this.code;
  }

  getMessage() {
    return this.message;
  }
}

module.exports = AmateurException;
