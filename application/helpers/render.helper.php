<?php namespace blogmarks\helper;

class render
{

  static function marks()
  {
    if (request_format() == 'rss') {
      set_header('Content-Type', 'application/rss+xml; charset=utf-8');
      layout(null, 'rss');
    }
    elseif (request_format() == 'atom') {
      if (get_param('export')) {
        set_header('Content-type', 'application/x-gzip');
        set_header('Content-Disposition', 'attachment; filename="bm3-backup.atom.xml.gz"');
        flush();
        ob_start();
        layout(null, 'atom');
        $atom = ob_get_clean();
        echo gzencode($atom);
      } else {
        set_header('Content-Type', 'application/atom+xml; charset=utf-8');
        layout(null, 'atom');
      }
    }
    elseif (get_param('more-marks')) {
      echo view('marks/more');
    }
    elseif (request()->header('X-PJAX')) {
      layout(view('marks/index'), 'pjax');
    }
    else {
      layout(view('marks/index'));
    }
  }

}

return new render;
