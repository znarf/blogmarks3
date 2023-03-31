<?php namespace blogmarks\service;

use
PhpAmqpLib\Message\AMQPMessage,
PhpAmqpLib\Connection\AMQPConnection;

class amqp
{

  protected $params;

  function params($params = null)
  {
    return $params ? $this->params = $params : $this->params;
  }

  protected $channel;

  function channel()
  {
    if (isset($this->channel)) {
      return $this->channel;
    }
    $params = self::params();
    if (empty($params)) {
      # Message Queue is not configured.
      return;
    }
    $connection = new AMQPConnection(
      $params['host'], $params['port'], $params['username'], $params['password']
    );
    $channel = $connection->channel();
    register_shutdown_function(function() use($connection, $channel) {
      $channel->close();
      $connection->close();
    });
    return $this->channel = $channel;
  }

  function queue_declare($queue)
  {
    $channel = self::channel();
    if ($channel) $channel->queue_declare($queue, false, true, false, false);
  }

  function json_message($message)
  {
    return new AMQPMessage(json_encode($message), ['content_type' => 'application/json']);
  }

  function publish($queue, $message)
  {
    $channel = self::channel();
    if ($channel) $channel->basic_publish(self::json_message($message), '', $queue);
  }

  function push($message, $queue)
  {
    self::queue_declare($queue);
    self::publish($queue, $message);
  }

  function consume($queue, $callback)
  {
    self::queue_declare($queue);
    $channel = self::channel();
    $channel->basic_qos(0, 10, false);
    $channel->basic_consume($queue, '', false, false, false, false, function($msg) use($callback) {
      $message = json_decode($msg->body, true);
      $channel = $msg->delivery_info['channel'];
      $delivery_tag = $msg->delivery_info['delivery_tag'];
      $ack = function() use($channel, $delivery_tag) {
        return $channel->basic_ack($delivery_tag);
      };
      $nack = function() use($channel, $delivery_tag) {
        return $channel->basic_nack($delivery_tag, false, true);
      };
      $cancel = function() use($channel, $consumer_tag) {
        return $channel->basic_cancel($consumer_tag);
      };
      $callback($message, $ack, $nack, $cancel);
    });
    while (count($channel->callbacks)) {
      $channel->wait();
    }
  }

}
