<?php namespace blogmarks\service;

use
Symfony\Component\Mime\Email as message,
Symfony\Component\Mailer\Mailer,
Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class email
{

  protected $params;

  function params($params = null)
  {
    return $params ? $this->params = $params : $this->params;
  }

  protected $mailer;

  function mailer()
  {
    if (empty($this->mailer)) {
      $params = $this->params();

      # Not using a DSN, credentials would need to be URL encoded
      $transport = new EsmtpTransport($params['host'], (int)$params['port']);
      $transport->setUsername($params['username']);
      $transport->setPassword($params['password']);

      $this->mailer = new Mailer($transport);
    }

    return $this->mailer;
  }

  function send($to, $subject, $body)
  {
    $params = $this->params();
    $mailer = $this->mailer();

    $email = (new message())
        ->from($params['from'])
        ->to($to)
        ->subject($subject)
        ->text($body);

    return $mailer->send($email);

  }
}
