<?php namespace blogmarks\model;

use
amateur\model\db,
amateur\model\cache,
amateur\model\table,
amateur\model\ressource;

class marks_tags extends table
{

  use \closurable_methods;

  public $namespace = __namespace__;

  public $classname = 'marks_tag';

  public $tablename = 'bm_marks_has_bm_tags';

  function from_mark($mark)
  {
    # From Cache
    $cache_key = "bm_marks_tags_id_{$mark->id}";
    $rows = cache::get($cache_key);
    # We expect an array, other values are invalid
    if (!is_array($rows)) {
      # From DB
      $rows = $this->fetch_all(['mark_id' => $mark->id]);
      cache::set($cache_key, $rows);
    }
    return $this->to_objects($rows);
  }

  function tag_mark($mark, $tags = [])
  {
    # Delete Previous Tags
    $this->delete(['mark_id' => $mark->id]);
    # Add Tags
    $rows = [];
    $objects = [];
    foreach ($tags as $_tag) {
      $_tag = trim($_tag);
      if (empty($_tag)) {
        continue;
      }
      $tag = $this->table('tags')->with_label($_tag);
      $row = [
        'mark_id'    => $mark->id,
        'tag_id'     => $tag->id,
        'user_id'    => $mark->author->id,
        'link_id'    => $mark->related->id,
        'label'      => $tag->label,
        'isHidden'   => 0,
        'visibility' => $mark->visibility,
      ];
      $rows[] = $row;
      $objects[] = self::create($row);
    }
    # Update Cache
    $cache_key = "bm_marks_tags_id_{$mark->id}";
    cache::set($cache_key, $rows);
    # Return
    return $objects;
  }

  function load_from_ids($ids)
  {
    # We consider that the first round of cache preload was already done while loading marks
    # So, we filter already loaded results
    $ids = array_filter($ids, function($id) { return !cache::loaded("bm_marks_tags_id_$id"); });
    # Jobs done?
    if (empty($ids)) return;
    # We iterate over chunk of 1000
    foreach (array_chunk($ids, 1000) as $ids_chunk) {
      # Then search in MySQL all tags matching remaining ids
      $result = $this
        ->select(['mark_id', 'tag_id', 'label', 'isHidden'])
        ->where(['mark_id' => $ids_chunk])
        ->execute();
      # We fetch results and group by mark
      $results = [];
      while ($row = db::fetch_assoc($result)) {
        $id = (int)$row['mark_id'];
        unset($row['mark_id']);
        $results[$id][] = $row;
      }
      # Then, we iterate over ids to store results in cache
      foreach ($ids_chunk as $id) {
        cache::set("bm_marks_tags_id_$id", isset($results[$id]) ? $results[$id] : []);
      }
    }
  }

}

class marks_tag extends ressource
{

  function __toString()
  {
    return $this->label;
  }

}

return model('marks_tags', table::instance('marks_tags', __namespace__));
