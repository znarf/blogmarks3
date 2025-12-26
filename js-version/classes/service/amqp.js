class amqp {
  constructor() {
    this.params_value = null;
    this.channel_value = null;
  }

  params(params = null) {
    if (params) {
      this.params_value = params;
    }
    return this.params_value;
  }

  channel() {
    if (this.channel_value) {
      return this.channel_value;
    }
    const params = this.params();
    if (!params) {
      return;
    }
    const connection = new AMQPConnection(
      params.host,
      params.port,
      params.username,
      params.password
    );
    const channel = connection.channel();
    register_shutdown_function(function () {
      channel.close();
      connection.close();
    });
    this.channel_value = channel;
    return this.channel_value;
  }

  queue_declare(queue) {
    const channel = this.channel();
    if (channel) {
      channel.queue_declare(queue, false, true, false, false);
    }
  }

  json_message(message) {
    return new AMQPMessage(json_encode(message), { content_type: 'application/json' });
  }

  publish(queue, message) {
    const channel = this.channel();
    if (channel) {
      channel.basic_publish(this.json_message(message), '', queue);
    }
  }

  push(message, queue) {
    this.queue_declare(queue);
    this.publish(queue, message);
  }

  consume(queue, callback) {
    this.queue_declare(queue);
    const channel = this.channel();
    channel.basic_qos(0, 10, false);
    channel.basic_consume(queue, '', false, false, false, false, function (msg) {
      const message = json_decode(msg.body, true);
      const current_channel = msg.delivery_info.channel;
      const delivery_tag = msg.delivery_info.delivery_tag;
      const ack = function () {
        return current_channel.basic_ack(delivery_tag);
      };
      const nack = function () {
        return current_channel.basic_nack(delivery_tag, false, true);
      };
      const cancel = function () {
        return current_channel.basic_cancel(consumer_tag);
      };
      callback(message, ack, nack, cancel);
    });
    while (channel.callbacks.length) {
      channel.wait();
    }
  }
}

module.exports = amqp;
