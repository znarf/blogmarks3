<?php namespace blogmarks\model;

use
amateur\model\table,
amateur\model\ressource;

class screenshots extends table
{

  public $namespace = __namespace__;

  public $classname = 'screenshot';

  public $tablename = 'bm_screenshots';

  public $primary = 'id';

  public $unique_indexes = ['id'];

  function load_from_marks($marks)
  {
    # Get list of link ids for which we need to retrieve screenshots
    $marks = array_filter($marks, function($mark) { return !$mark->attribute('screenshot'); });
    $link_ids = array_map(function($mark) { return (int)$mark->attribute('related'); }, $marks);
    # Nothing to do?
    if (empty($link_ids)) return;
    # Query
    $query = $this->select(['link', 'url'])->where(['link' => $link_ids, 'status' => 1])->order_by('created');
    # Fetch results as an associative array
    $results = $query->fetch_key_values('link', 'url');
    # Iterate over marks and update cache infos with the screenshot if available
    foreach ($marks as $mark) {
      $link_id = $mark->attribute('related');
      $screenshot = isset($results[$link_id]) ? $results[$link_id] : $mark->default_screenshot();
      $mark->cache_attribute('screenshot', $screenshot);
    }
  }

}

class screenshot extends ressource {}

return model('screenshots', table::instance('screenshots', __namespace__));
