<?php namespace blogmarks;

# Don't show details of unexpected errors to visitors, log them instead
function default_error($code = 500, $message = 'Application Error', $trace = '')
{
  if ($code >= 500) {
    error_log("{$message}\n{$trace}");
    $message = _('Application Error');
  }
  return '<h2>' . text($message) . '</h2>';
}
