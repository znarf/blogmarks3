<?php

use \Amateur\Model\Table as Table;
use \Amateur\Model\Ressource as Ressource;

class MarksTags extends Table
{

  public $classname = 'MarksTag';
  public $tablename = 'bm_marks_has_bm_tags';
  public $primary = 'id';
  public $unique_indexes = ['id'];

  function from_mark($mark)
  {
    # From Cache
    $cache_key = "bm_marks_tags_id_{$mark->id}";
    $objects = cache_get($cache_key);
    # We expect an array, other values are invalid
    if (is_array($objects)) {
      return $objects;
    }
    # From DB
    $result = db_search($this->tablename, ['where' => ['mark_id' => $mark->id]]);
    if ($result) {
      $objects = db_fetch_objects($result, 'MarksTag');
      cache_set($cache_key, $objects);
      return $objects;
    }
    # Default
    return [];
  }

  function tag_mark($mark, $tags = [])
  {
    # Delete Previous Tags
    db_delete($this->tablename, ['mark_id' => $mark->id]);
    # Add Tags
    $objects = [];
    foreach ($tags as $_tag) {
      $_tag = trim($_tag);
      if (empty($_tag)) {
        continue;
      }
      $tag = model('tags')->with_label($_tag);
      $objects[] = self::create([
        'mark_id'  => $mark->id,
        'tag_id'   => $tag->id,
        'user_id'  => $mark->author->id,
        'link_id'  => $mark->related->id,
        'label'    => $tag->label,
        'isHidden' => 0
      ]);
    }
    # Update Cache
    $cache_key = "bm_marks_tags_id_{$mark->id}";
    cache_set($cache_key, $objects);
    # Return
    return $objects;
  }

}

class MarksTag extends Ressource
{

  function __toString()
  {
    return $this->label;
  }

}

return instance('MarksTags');
