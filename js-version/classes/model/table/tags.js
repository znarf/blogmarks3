class tags extends table {
  constructor() {
    super();
    this.classname = '\\blogmarks\\model\\resource\\tag';
    this.tablename = 'bm_tags';
    this.unique_indexes = ['id', 'label'];
  }

  cache_key(key, value, type = 'raw') {
    const hashed = key === 'label' ? md5(value) : value;
    return super.cache_key(key, hashed, type);
  }

  with_label(label) {
    return this.get_one('label', label) || this.create({ label });
  }

  query_latests(interval = null) {
    const query = this
      .select('mht.id, mht.label as label, COUNT(*) as count')
      .from('bm_marks as m, bm_marks_has_bm_tags as mht')
      .where('mht.mark_id = m.id')
      .and_where({ 'm.visibility': 0, 'm.display': 1 })
      .and_where({ 'mht.isHidden': 0, 'mht.visibility': 0, 'mht.display': 1 })
      .group_by('mht.tag_id')
      .limit(1000);
    if (interval) {
      if (db.driver() == 'sqlite') {
        query.and_where(`m.published > datetime('now', '-${interval}')`);
      } else {
        query.and_where(`m.published > DATE_SUB(NOW(), INTERVAL ${interval})`);
      }
    }
    return query;
  }

  query_related_with(tag, privateValue = false) {
    const query = this
      .select('mht2.tag_id as id, mht2.label, COUNT(*) as count')
      .from('bm_marks_has_bm_tags as mht1, bm_marks_has_bm_tags as mht2')
      .where('mht2.mark_id = mht1.mark_id')
      .and_where({ 'mht1.tag_id': tag.id })
      .and_where('mht2.tag_id != ' + db.quote(tag.id))
      .group_by('mht2.tag_id');
    if (!privateValue) {
      query.and_where({ 'mht1.isHidden': 0, 'mht1.visibility': 0, 'mht1.display': 1 });
      query.and_where({ 'mht2.isHidden': 0, 'mht2.visibility': 0, 'mht2.display': 1 });
    }
    return query;
  }

  query_from_user(user, privateValue = false) {
    const query = this
      .select('id, label, COUNT(*) as count')
      .from('bm_marks_has_bm_tags')
      .where({ user_id: user.id })
      .group_by('tag_id');
    if (!privateValue) {
      query.and_where({ isHidden: 0, visibility: 0 });
    }
    return query;
  }

  query_from_user_related_with(user, tag, privateValue = false) {
    const query = this
      .select('mht2.tag_id as id, mht2.label, COUNT(*) as count')
      .from('bm_marks_has_bm_tags as mht1, bm_marks_has_bm_tags as mht2')
      .where('mht2.mark_id = mht1.mark_id')
      .and_where({ 'mht1.tag_id': tag.id })
      .and_where({ 'mht1.user_id': user.id })
      .and_where('mht2.tag_id != ' + db.quote(tag.id))
      .group_by('mht2.tag_id');
    if (!privateValue) {
      query.and_where({ 'mht2.isHidden': 0, 'mht2.visibility': 0 });
    }
    return query;
  }

  private_ratios_for_user(user) {
    const cache_key = `bm_marks_has_bm_tags_private_ratios_${user.id}`;
    const objects = cache.get(cache_key);
    if (Array.isArray(objects)) {
      return objects;
    }
    const ratios = this
      .table('marks_tags')
      .select('(SUM(isHidden) / COUNT(*) * 100) AS ratio, label')
      .where({ user_id: user.id })
      .group_by('label')
      .having('ratio > 0')
      .fetch_key_values('label', 'ratio');
    cache.set(cache_key, ratios);
    return ratios;
  }
}

module.exports = tags;
