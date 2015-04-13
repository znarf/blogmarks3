<?php namespace blogmarks;

function flash_message($message = null)
{
  if ($message) {
    return $_SESSION['message'] = $message;
  }
  elseif (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
    return $message;
  }
}
