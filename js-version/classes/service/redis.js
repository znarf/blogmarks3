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
    const { createClient } = require('redis');
    const params = this.params() || {};
    const url = params.url || process.env.REDIS_URL;
    if (!url) {
      return;
    }
    const client = createClient({ url });
    client.on('error', () => {});
    client.connect();
    this.connection_value = client;
    return client;
  }
}

module.exports = redis;
