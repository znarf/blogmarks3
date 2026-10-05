<?php namespace blogmarks;

# Don't show details of unexpected errors to visitors, log them instead
function default_error($code = 500, $message = 'Application Error', $trace = '')
{
  if ($code >= 500) {
    error_log("{$message}\n{$trace}");
  }
  # Development: show the message and trace
  if (flag('debug') && $trace) {
    ob_start();
    partial('trace', compact('code', 'message', 'trace'));
    return ob_get_clean();
  }
  if ($code >= 500) {
    $message = _('Application Error');
  }
  return '<h2>' . text($message) . '</h2>';
}
