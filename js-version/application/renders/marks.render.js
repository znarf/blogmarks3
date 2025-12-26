module.exports = function () {
  const requestFormat = request_format();

  if (requestFormat === 'json') {
    response_header('Content-Type', 'application/json; charset=utf-8');
    view('marks/json');
  } else if (requestFormat === 'rss') {
    response_header('Content-Type', 'application/rss+xml; charset=utf-8');
    view('marks/rss');
  } else if (requestFormat === 'atom') {
    response_header('Content-Type', 'application/atom+xml; charset=utf-8');
    view('marks/atom');
    if (get_param('export')) {
      if (flag('compress_export')) {
        response_header('Content-Type', 'application/x-gzip');
        response_header('Content-Disposition', 'attachment; filename="bm3-backup.atom.xml.gz"');
        response_content(gzencode(response_content()));
      } else {
        response_header('Content-Type', 'application/x-download');
        response_header('Content-Disposition', 'attachment; filename="bm3-backup.atom.xml"');
      }
    }
  } else if (request_param('more-marks')) {
    view('marks/more');
  } else {
    view('marks/index');
    layout(request_header('X-PJAX') ? 'pjax' : 'default');
  }
};
