<?php namespace Blogmarks;

require_once amateur_dir . '/core/app.class.php';

class App extends \Amateur\Core\App
{

  function marks($marks = null)
  {
    if ($marks) {
      $this->helper('container')->marks($marks);
    }
    if ($this->request_format() == 'rss') {
      $this->response()->set_header('Content-Type', 'application/rss+xml; charset=utf-8');
      $this->layout(null, 'rss');
    }
    elseif ($this->request_format() == 'atom') {
      $this->response()->set_header('Content-Type', 'application/atom+xml; charset=utf-8');
      $this->layout(null, 'atom');
    }
    elseif ($this->request()->param('more-marks')) {
      echo $this->view('marks/more');
    }
    elseif ($this->request()->header('X-PJAX')) {
      $this->layout($this->view('marks/index'), 'pjax');
    }
    else {
      $this->layout($this->view('marks/index'));
    }
  }

  public $default_layout = 'default';

  function layout($content = '', $name = null)
  {
    if (!$name) $name = $this->default_layout;
    return parent::layout($content, $name);
  }

  public $default_format = 'html';

  function request_format()
  {
    static $request_format;
    # Cached
    if ($request_format) {
      return $request_format;
    }
    # Format Param
    $format = get_param('format');
    if ($format && in_array($format, ['json', 'rss', 'atom'])) {
      return $request_format = $format;
    }
    # Accept Header
    $accept = request_header('Accept');
    $mime = [
      'application/json'     => 'json',
      'application/rss+xml'  => 'rss',
      'application/atom+xml' => 'atom'
    ];
    if ($accept && isset($mime[$accept])) {
      return $request_format = $mime[$accept];
    }
    # Default
    return $request_format = $this->default_format;
  }

}
