<?php

$render = anonymous_class();

$render->marks = function() {
  if (request_format() == 'rss') {
    response_header('Content-Type', 'application/rss+xml; charset=utf-8');
    layout(null, 'rss');
  }
  elseif (request_format() == 'atom') {
    if (get_param('export')) {
      response_header('Content-type', 'application/x-gzip');
      response_header('Content-Disposition', 'attachment; filename="bm3-backup.atom.xml.gz"');
      flush();
      ob_start();
      layout(null, 'atom');
      $atom = ob_get_clean();
      ok(gzencode($atom));
    } else {
      response_header('Content-Type', 'application/atom+xml; charset=utf-8');
      layout(null, 'atom');
    }
  }
  elseif (get_param('more-marks')) {
    ok(view('marks/more'));
  }
  elseif (request_header('X-PJAX')) {
    layout(view('marks/index'), 'pjax');
  }
  else {
    layout(view('marks/index'));
  }
};

return $render;
