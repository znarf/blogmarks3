<?php namespace blogmarks\helper;

class form
{

  static function generate_phrase($length = 64)
  {
    $chars = '1234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $i = 0;
    $phrase = '';
    while ($i < $length) {
        $phrase .= $chars{mt_rand(0, strlen($chars)-1)};
        $i++;
    }
    return $phrase;
  }

  static function generate_token($key)
  {
    return $_SESSION["csrf_{$key}"] = self::generate_phrase();;
  }

  static function check_token($key, $token)
  {
    if (empty($_SESSION["csrf_{$key}"]))
      throw http_error(400, 'Missing session token.');
    if (empty($token))
      throw http_error(400, 'Missing form token.');
    if ($_SESSION["csrf_{$key}"] != $token)
      throw http_error(400, 'Invalid token.');
  }

}

$form = new form;

replaceable('check_token', [$form, 'check_token']);
replaceable('generate_token', [$form, 'generate_token']);

return $form;
