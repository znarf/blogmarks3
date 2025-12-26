class redis {
  constructor() {
    this.params_value = null;
    this.connection_value = null;
  }

  params(params = null) {
    if (params) {
      this.params_value = params;
    }
    return this.params_value;
  }

  connection(connection = null) {
    if (connection) {
      this.connection_value = connection;
    }
    if (this.connection_value) {
      return this.connection_value;
    }
    if (!class_exists('\\redis', false) || !this.params()) {
      return;
    }
    const client = new Redis();
    client.pconnect(this.params().host);
    this.connection_value = client;
    return this.connection_value;
  }
}

module.exports = redis;
