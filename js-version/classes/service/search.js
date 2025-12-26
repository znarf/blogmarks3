class search {
  constructor() {
    this.params_value = null;
    this.client_value = null;
  }

  params(params = null) {
    if (params) {
      this.params_value = params;
    }
    return this.params_value;
  }

  client(client = null) {
    if (client) {
      this.client_value = client;
    }
    if (this.client_value) {
      return this.client_value;
    }
    if (!this.params()) {
      return;
    }
    if (this.params().username && this.params().password) {
      const credentials = base64_encode(
        this.params().username + ':' + this.params().password
      );
      this.params().headers = this.params().headers || {};
      this.params().headers.authorization = `basic ${credentials}`;
    }
    const clientValue = new Client(this.params());
    this.client_value = clientValue;
    return this.client_value;
  }

  base_url() {
    return 'http://' + this.params().host + ':' + this.params().port;
  }

  delete(url) {
    if (this.client()) {
      new request().delete(this.base_url() + url);
    }
  }

  search(url, query = []) {
    const response = new request().post_json(this.base_url() + url + '/_search', query);
    const result = json_decode(response.body, true);
    return result;
  }

  count(url, query = []) {
    const response = new request().post_json(this.base_url() + url + '/_count', query);
    const result = json_decode(response.body, true);
    return result;
  }
}

module.exports = search;
