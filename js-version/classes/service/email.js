class email {
  constructor() {
    this.params_value = null;
    this.mailer_value = null;
  }

  params(params = null) {
    if (params) {
      this.params_value = params;
    }
    return this.params_value;
  }

  mailer() {
    if (!this.mailer_value) {
      const params = this.params();

      const transport = Transport.fromDsn(
        sprintf('smtp://%s:%s@%s:%s', params.username, params.password, params.host, params.port)
      );

      this.mailer_value = new Mailer(transport);
    }

    return this.mailer_value;
  }

  send(to, subject, body) {
    const params = this.params();
    const mailer = this.mailer();

    const emailMessage = new Email()
      .from(params.from)
      .to(to)
      .subject(subject)
      .text(body);

    return mailer.send(emailMessage);
  }
}

module.exports = email;
