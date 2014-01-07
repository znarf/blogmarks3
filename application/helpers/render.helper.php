<?php

$render = anonymous_class();

$render->marks = function() {
  if (request_format() == 'rss') {
    response_header('Content-Type', 'application/rss+xml; charset=utf-8');
    ok(view('marks/rss'));
  }
  elseif (request_format() == 'atom') {
    if (get_param('export')) {
      response_header('Content-Type', 'application/x-gzip');
      response_header('Content-Disposition', 'attachment; filename="bm3-backup.atom.xml.gz"');
      ok(gzencode(view('marks/atom')));
    } else {
      response_header('Content-Type', 'application/atom+xml; charset=utf-8');
      ok(view('marks/atom'));
    }
  }
  elseif (get_param('more-marks')) {
    ok(view('marks/more'));
  }
  elseif (request_header('X-PJAX')) {
    layout('pjax', view('marks/index'));
  }
  else {
    layout('default', view('marks/index'));
  }
};

return $render;
