<?php namespace blogmarks\service;

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

      $transport = (new \Swift_SmtpTransport($params['host'], $params['port']))
        ->setUsername($params['username'])
        ->setPassword($params['password']);

      $this->mailer = new \Swift_Mailer($transport);
    }

    return $this->mailer;
  }

  function send($to, $subject, $body)
  {
    $params = $this->params();

    $mailer = $this->mailer();

    $message = (new \Swift_Message($subject))
      ->setFrom($params['from'])
      ->setTo($to)
      ->setBody($body);

    return $mailer->send($message);

  }
}
