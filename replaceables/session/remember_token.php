<?php namespace blogmarks;

# Signed "remember me" token: user id, expiration and signature
# The password hash is part of the signature, changing the password invalidates tokens
function remember_token($user, $expires)
{
  $data = "{$user->id}:{$expires}";
  $signature = hash_hmac('sha256', "{$data}:{$user->pass}", blogmarks::flag('secret'));
  return "{$data}:{$signature}";
}
