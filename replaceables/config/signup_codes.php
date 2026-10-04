<?php namespace blogmarks;

# Invitation codes required to sign up, e.g. flag('signup_codes', ['alpha', 'beta'])
function signup_codes()
{
  return flag('signup_codes') ?: [];
}
