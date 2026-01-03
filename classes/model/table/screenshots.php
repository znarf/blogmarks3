<?php namespace blogmarks\model\table;

use amateur\model\db;

use blogmarks\model\table;

class screenshots extends table
{

  public $tablename = 'bm_screenshots';

  function create($set = [])
  {
    return parent::create(['created' => db::now()] + $set);
  }

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

  function for_mark($mark)
  {
      # TODO: should get the one for the published/created date first
      $query = $this
        ->select('url')
        ->where(['link' => $mark->link_id, 'status' => 1])
        ->order_by('created DESC');
      return $query->fetch_one();
  }

  function ensure_entry_exists_for_mark($mark)
  {
    $now = db::now();
    $params = ['link' => $mark->link_id];
    $existing = $this->where($params)->and_where("created > DATE_SUB('$now', INTERVAL 1 DAY)");
    if ($existing->count() == 0) {
      $screenshot = $this->create($params + ['status' => 0]);
      $this->service('amqp')->push(['id' => $screenshot->id], 'take_screenshot');
    }
  }

}
