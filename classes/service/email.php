<?php namespace blogmarks\service;

use
Symfony\Component\Mime\Email,
Symfony\Component\Mailer\Mailer,
Symfony\Component\Mailer\Transport;

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

      $transport = Transport::fromDsn(sprintf(
          'smtp://%s:%s@%s:%s',
          $params['username'],
          $params['password'],
          $params['host'],
          $params['port']
      ));

      $this->mailer = new Mailer($transport);
    }

    return $this->mailer;
  }

  function send($to, $subject, $body)
  {
    $params = $this->params();
    $mailer = $this->mailer();

    $email = (new Email())
        ->from($params['from'])
        ->to($to)
        ->subject($subject)
        ->text($body);

    return $mailer->send($email);

  }
}
