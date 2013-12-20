<?php namespace blogmarks\model\table;

class screenshots extends \blogmarks\model\table
{

  public $tablename = 'bm_screenshots';

  function preload_for_marks($marks)
  {
    # Get list of link ids for which we need to retrieve screenshots
    $marks = array_filter($marks, function($mark) { return !isset($mark->attributes['screenshot']); });
    $link_ids = array_map(function($mark) { return (int)$mark->attributes['related']; }, $marks);
    # If everything already loaded
    if (empty($link_ids)) {
      return;
    }
    # Query
    $query = $this->select(['link', 'url'])->where(['link' => $link_ids, 'status' => 1])->order_by('created');
    # Fetch results as an associative array
    $results = $query->fetch_key_values('link', 'url');
    # Iterate over marks and update cache infos with the screenshot if available
    foreach ($marks as $mark) {
      $link_id = $mark->link_id;
      $screenshot = isset($results[$link_id]) ? $results[$link_id] : $mark->default_screenshot();
      $mark->cache_attribute('screenshot', $screenshot);
    }
  }

}
