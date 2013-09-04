<?php

use \Amateur\Model\Table as Table;
use \Amateur\Model\Ressource as Ressource;

class Screenshots extends Table
{

  public $classname = 'Screenshot';
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
    # Iterate over results and store in a temporary array
    $results = [];
    foreach ($query->fetch_all() as $row) {
      $link_id = (int)$row['link'];
      $results[$link_id] = $row['url'];
    }
    # Iterate over marks and update cache infos with the screenshot if available
    foreach ($marks as $mark) {
      $link_id = $mark->attribute('related');
      $screenshot = isset($results[$link_id]) ? $results[$link_id] : $mark->default_screenshot();
      $mark->cache_attribute('screenshot', $screenshot);
    }
  }

}

class Screenshot extends Ressource {}

return new Screenshots;
