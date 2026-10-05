<?php
# Development error page (flag 'debug'), see replaceables/error/default_error.php

$frames = [];
foreach (explode("\n", trim($trace)) as $line) {
  if (preg_match('/^#(\d+) (?:(.+)\((\d+)\)|\[internal function\]): (.+)$/', $line, $matches)) {
    $file = $matches[2] ?? '';
    $frames[] = [
      'number'   => $matches[1],
      'file'     => $file,
      'line'     => (int)($matches[3] ?? 0),
      'call'     => $matches[4],
      'relative' => str_starts_with($file, root_dir . '/') ? substr($file, strlen(root_dir) + 1) : $file,
      'vendor'   => !$file || str_contains($file, '/vendor/'),
    ];
  }
}

# Source lines around the given line, if the file is readable
$excerpt = function ($file, $line, $context = 6) {
  if (!$line || !is_file($file) || !is_readable($file)) {
    return [];
  }
  $lines = file($file, FILE_IGNORE_NEW_LINES);
  $start = max(1, $line - $context);
  $end = min(count($lines), $line + $context);
  $excerpt = [];
  for ($i = $start; $i <= $end; $i++) {
    $excerpt[$i] = $lines[$i - 1];
  }
  return $excerpt;
};

$first_app_frame = null;
foreach ($frames as $index => $frame) {
  if (!$frame['vendor']) {
    $first_app_frame = $index;
    break;
  }
}
?>
<style>
.bm-trace { margin: 20px 0; font-size: 13px; }
.bm-trace-header { padding: 16px 20px; border-radius: 6px 6px 0 0; background: #b52b27; color: #fff; }
.bm-trace-code { font-size: 12px; font-weight: bold; letter-spacing: .05em; text-transform: uppercase; opacity: .8; }
.bm-trace-message { margin-top: 6px; font-size: 18px; line-height: 1.4; word-break: break-word; }
.bm-trace-frames { margin: 0; padding: 0; list-style: none; border: 1px solid #ddd; border-top: 0; border-radius: 0 0 6px 6px; overflow: hidden; }
.bm-trace-frame { border-top: 1px solid #eee; }
.bm-trace-frame:first-child { border-top: 0; }
.bm-trace-frame summary { display: flex; gap: 12px; align-items: baseline; padding: 8px 16px; cursor: pointer; list-style: none; }
.bm-trace-frame summary::-webkit-details-marker { display: none; }
.bm-trace-frame summary:hover { background: #f7f7f7; }
.bm-trace-number { flex: none; width: 24px; color: #999; text-align: right; font-family: Menlo, Consolas, monospace; }
.bm-trace-call { flex: 1; min-width: 0; font-family: Menlo, Consolas, monospace; color: #222; word-break: break-all; }
.bm-trace-file { flex: none; max-width: 45%; color: #777; font-family: Menlo, Consolas, monospace; text-align: right; word-break: break-all; }
.bm-trace-frame.is-vendor summary { opacity: .55; }
.bm-trace-frame.is-app summary { border-left: 3px solid #b52b27; padding-left: 13px; }
.bm-trace-source { margin: 0; padding: 8px 0; background: #1e1e1e; color: #ddd; font-family: Menlo, Consolas, monospace; font-size: 12px; line-height: 1.6; overflow-x: auto; }
.bm-trace-source div { padding: 0 16px; white-space: pre; }
.bm-trace-source span { display: inline-block; width: 40px; margin-right: 12px; color: #777; text-align: right; user-select: none; }
.bm-trace-source .is-current { background: #5a1d1d; color: #fff; }
.bm-trace-source .is-current span { color: #f0a0a0; }
</style>

<div id="content" class="fullwidth">
  <div id="content-inner">
    <div class="bm-trace">
      <div class="bm-trace-header">
        <div class="bm-trace-code">Error <?= (int)$code ?></div>
        <div class="bm-trace-message"><?= text($message) ?></div>
      </div>
      <ol class="bm-trace-frames">
        <?php foreach ($frames as $index => $frame) : ?>
        <?php $source = $frame['vendor'] ? [] : $excerpt($frame['file'], $frame['line']) ?>
        <li class="bm-trace-frame <?= $frame['vendor'] ? 'is-vendor' : 'is-app' ?>">
          <details <?php if ($index === $first_app_frame) echo 'open' ?>>
            <summary>
              <span class="bm-trace-number"><?= text($frame['number']) ?></span>
              <span class="bm-trace-call"><?= text($frame['call']) ?></span>
              <span class="bm-trace-file"><?= text($frame['relative'] ?: '[internal]') ?><?php if ($frame['line']) echo ':' . $frame['line'] ?></span>
            </summary>
            <?php if ($source) : ?>
            <div class="bm-trace-source">
              <?php foreach ($source as $number => $code_line) : ?>
              <div<?php if ($number == $frame['line']) echo ' class="is-current"' ?>><span><?= $number ?></span><?= text($code_line) ?></div>
              <?php endforeach ?>
            </div>
            <?php endif ?>
          </details>
        </li>
        <?php endforeach ?>
      </ol>
    </div>
  </div>
</div>
