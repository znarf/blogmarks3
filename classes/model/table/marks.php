<?php namespace blogmarks\model\table;

use
amateur\model\db,
amateur\model\cache;

class marks extends \blogmarks\model\table
{

  public $classname = '\blogmarks\model\ressource\mark';

  public $tablename = 'bm_marks';

  public $collection_indexes = ['author'];

  function create($params = [])
  {
    $params += ['contentType' => 'text'];
    $params += ['published' => db::now(), 'updated' => db::now()];
    return parent::create($params);
  }

  function update($mark, $params = [])
  {
    $params += ['updated' => db::now()];
    return parent::update($mark, $params);
  }

  function delete($mark)
  {
    return parent::delete($mark);
  }

  function get($arg)
  {
    if (!is_array($arg)) {
      return $this->get_one('id', $arg);
    }
    # Ids
    $ids = array_map('intval', $arg);
    # Preload cache keys
    $cache_keys = [];
    foreach ($ids as $id) array_push($cache_keys, "bm_marks_raw_id_$id", "bm_marks_tags_id_$id");
    cache::preload($cache_keys);
    # Get marks objects
    $marks = $this->get_all('id', $ids);
    # Load marks tags
    $this->table('marks_tags')->preload_for_mark_ids($ids);
    # Load links
    $this->table('links')->preload_for_marks($marks);
    # Load screenshots
    $this->table('screenshots')->preload_for_marks($marks);
    # Preload user cache keys
    $user_keys = array_map(function($mark) { return "bm_users_raw_id_" . $mark->attributes['author']; }, $marks);
    cache::preload($user_keys);
    # Return
    return $marks;
  }

  # Queries

  function query_ids_and_ts($where = [])
  {
    $query = $this
      ->select('id, UNIX_TIMESTAMP(published) as ts')
      ->where($where);
    if (db::driver() == 'sqlite') {
      $query->select("id, strftime('%s', published) as ts");
    }
    return $query;
  }

  function query_latest_ids_and_ts()
  {
    $query = $this
      ->query_ids_and_ts(['visibility' => 0, 'display' => 1])
      ->order_by('published DESC')
      ->limit(1000);
    return $query;
  }

  function query_ids_and_ts_from_user($user, $params = [])
  {
    $query = $this
      ->query_ids_and_ts(['author' => $user->id]);
    if (empty($params['private'])) {
      $query->and_where(['visibility' => 0]);
    }
    return $query;
  }

  function query_ids_and_ts_with_tag($tag)
  {
    $query = $this
      ->select('m.id, UNIX_TIMESTAMP(m.published) as ts')
      ->from('bm_marks as m, bm_marks_has_bm_tags as mht')
      ->where('m.id = mht.mark_id')
      ->and_where(['mht.tag_id' => $tag->id, 'm.visibility' => 0, 'm.display' => 1]);
    if (db::driver() == 'sqlite') {
      $query->select("m.id, strftime('%s', m.published) as ts");
    }
    return $query;
  }

  function query_ids_and_ts_from_user_with_tag($user, $tag, $params = [])
  {
    $query = $this
      ->select('m.id, UNIX_TIMESTAMP(m.published) as ts')
      ->from('bm_marks as m, bm_marks_has_bm_tags as mht')
      ->where('m.id = mht.mark_id')
      ->and_where(['m.author' => $user->id, 'mht.tag_id' => $tag->id]);
    if (empty($params['private'])) {
      $query->and_where(['m.visibility' => 0]);
    }
    if (db::driver() == 'sqlite') {
      $query->select("m.id, strftime('%s', m.published) as ts");
    }
    return $query;
  }

}
