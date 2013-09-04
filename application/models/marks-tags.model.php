<?php

use \Amateur\Model\Db as Db;
use \Amateur\Model\Cache as Cache;
use \Amateur\Model\Table as Table;
use \Amateur\Model\Ressource as Ressource;

class MarksTags extends Table
{

  use Closurize;

  public $classname = 'MarksTag';
  public $tablename = 'bm_marks_has_bm_tags';
  public $primary = 'id';
  public $unique_indexes = ['id'];

  function from_mark($mark)
  {
    # From Cache
    $cache_key = "bm_marks_tags_id_{$mark->id}";
    $rows = Cache::get($cache_key);
    # We expect an array, other values are invalid
    if (!is_array($rows)) {
      # From DB
      $rows = $this->fetch_all(['mark_id' => $mark->id]);
      Cache::set($cache_key, $rows);
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
      $tag = model('tags')->with_label($_tag);
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
    Cache::set($cache_key, $rows);
    # Return
    return $objects;
  }

  function fetch_from_user($user, $status = 'public')
  {
    $query = $this->select('label, COUNT(*) as count')->where(['user_id' => $user->id]);
    if ($status == 'public') {
      $query->and_where(['isHidden' => 0]);
    }
    return $query->group_by('tag_id')->fetch_all();
  }

  function load_from_ids($ids)
  {
    # We consider that the first round of cache preload was already done while loading marks
    # So, we filter already loaded results
    $ids = array_filter($ids, function($id) { return !Cache::loaded("bm_marks_tags_id_$id"); });
    # Jobs done?
    if (empty($ids)) return;
    # We iterate over chunk of 1000
    foreach (array_chunk($ids, 1000) as $ids_chunk) {
      # Then search in MySQL all tags matching remaining ids
      $result = $this->select()->where(['mark_id' => $ids_chunk])->execute();
      # We fetch results and group by mark
      $results = [];
      while ($row = Db::fetch_assoc($result)) {
        $id = (int)$row['mark_id'];
        $results[$id][] = $row;
      }
      # Then, we iterate over ids to store results in cache
      foreach ($ids_chunk as $id) {
        Cache::set("bm_marks_tags_id_$id", isset($results[$id]) ? $results[$id] : []);
      }
    }
  }

}

class MarksTag extends Ressource
{

  function __toString()
  {
    return $this->label;
  }

}

return new MarksTags;
