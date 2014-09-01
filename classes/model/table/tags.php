<?php namespace blogmarks\model\table;

use
amateur\model\db,
amateur\model\cache;

class tags extends \blogmarks\model\table
{

  use \amateur\magic\closurable_methods;

  public $classname = '\blogmarks\model\resource\tag';

  public $tablename = 'bm_tags';

  public $unique_indexes = ['id', 'label'];

  function with_label($label)
  {
    $tag = self::get_one('label', $label);
    return $tag ? $tag : self::create(['label' => $label]);
  }

  # Queries

  function query_latests()
  {
    $query = $this
      ->select('mht.id, mht.label as label, COUNT(*) as count')
      ->from('bm_marks as m, bm_marks_has_bm_tags as mht')
      ->where('mht.mark_id = m.id')
      ->and_where(['m.visibility' => 0, 'mht.isHidden' => 0])
      ->group_by('mht.tag_id')
      ->limit(1000);
    if (db::driver() == 'sqlite') {
      $query->and_where("m.published > datetime('now', '-1 year')");
    }
    else {
      $query->and_where("m.published > DATE_SUB(NOW(), INTERVAL 1 YEAR)");
    }
    return $query;
  }

  function query_related_with($tag, $private = false)
  {
    $query = $this
      ->select('mht2.tag_id as id, mht2.label, COUNT(*) as count')
      ->from('bm_marks_has_bm_tags as mht1, bm_marks_has_bm_tags as mht2')
      ->where('mht2.mark_id = mht1.mark_id')
      ->and_where(['mht1.tag_id' => $tag->id])
      ->and_where('mht2.tag_id != ' . db::quote($tag->id))
      ->group_by('mht2.tag_id');
    if (!$private) {
      $query->and_where(['mht2.isHidden' => 0]);
      if (!flag('db_old_schema')) {
        $query->and_where(['mht2.visibility' => 0]);
      }
    }
    return $query;
  }

  function query_from_user($user, $private = false)
  {
    $query = $this
      ->select('id, label, COUNT(*) as count')
      ->from('bm_marks_has_bm_tags')
      ->where(['user_id' => $user->id])
      ->group_by('tag_id');
    if (!$private) {
      $query->and_where(['isHidden' => 0]);
      if (!flag('db_old_schema')) {
        $query->and_where(['visibility' => 0]);
      }
    }
    return $query;
  }

  function query_from_user_related_with($user, $tag, $private = false)
  {
   return $this->query_related_with($tag, $private)->and_where(['mht1.user_id' => $user->id]);
  }

  # Ratios

  function private_ratios_for_user($user)
  {
    # From Cache
    $cache_key = "bm_marks_has_bm_tags_private_ratios_{$user->id}";
    $objects = cache::get($cache_key);
    if (is_array($objects)) {
      return $objects;
    }
    # From DB
    $ratios = $this->table('marks_tags')
      ->select('(SUM(isHidden) / COUNT(*) * 100) AS ratio, label')
      ->where(['user_id' => $user->id])
      ->group_by('label')
      ->having('ratio > 0')
      ->fetch_key_values('label', 'ratio');
    # Set Cache
    cache::set($cache_key, $ratios);
    # Return
    return $ratios;
  }

}
