<?php namespace blogmarks;

# Set (or delete, without token) the "remember me" cookie
function remember_cookie($token = '', $expires = 1)
{
  $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
    || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https');
  setcookie('remember', $token, [
    'expires'  => $expires,
    'path'     => '/',
    'secure'   => $https,
    'httponly' => true,
    'samesite' => 'Lax',
  ]);
}
