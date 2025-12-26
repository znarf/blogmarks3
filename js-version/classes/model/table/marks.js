class marks extends table {
  constructor() {
    super();
    this.classname = '\\blogmarks\\model\\resource\\mark';
    this.tablename = 'bm_marks';
    this.collection_indexes = ['author'];
  }

  create(params = []) {
    params = { contentType: 'text', ...params };
    params = { published: db.now(), updated: db.now(), ...params };
    return super.create(params);
  }

  update(mark, params = []) {
    params = { updated: db.now(), ...params };
    return super.update(mark, params);
  }

  delete(mark) {
    return super.delete(mark);
  }

  get(arg) {
    if (!Array.isArray(arg)) {
      return this.get_one('id', arg);
    }
    const ids = arg.map((id) => parseInt(id, 10));
    const cache_keys = [];
    ids.forEach((id) => {
      cache_keys.push(`bm_marks_raw_id_${id}`, `bm_marks_tags_id_${id}`);
    });
    cache.preload(cache_keys);
    const marks = this.get_all('id', ids);
    this.table('marks_tags').preload_for_mark_ids(ids);
    this.table('links').preload_for_marks(marks);
    this.table('screenshots').preload_for_marks(marks);
    const user_keys = marks.map((mark) => `bm_users_raw_id_${mark.attributes.author}`);
    cache.preload(user_keys);
    return marks;
  }

  query_ids_and_ts(where = []) {
    let query = this.select('id, UNIX_TIMESTAMP(published) as ts').where(where);
    if (db.driver() == 'sqlite') {
      query = query.select("id, strftime('%s', published) as ts");
    }
    return query;
  }

  query_latest_ids_and_ts() {
    return this
      .query_ids_and_ts({ visibility: 0, display: 1 })
      .order_by('published DESC')
      .limit(1000);
  }

  query_ids_and_ts_from_user(user, params = []) {
    const query = this.query_ids_and_ts({ author: user.id });
    if (!params.private) {
      query.and_where({ visibility: 0 });
    }
    return query;
  }

  query_ids_and_ts_with_tag(tag) {
    let query = this
      .select('m.id, UNIX_TIMESTAMP(m.published) as ts')
      .from('bm_marks as m, bm_marks_has_bm_tags as mht')
      .where('m.id = mht.mark_id')
      .and_where({ 'mht.tag_id': tag.id, 'm.visibility': 0, 'm.display': 1 });
    if (db.driver() == 'sqlite') {
      query = query.select("m.id, strftime('%s', m.published) as ts");
    }
    return query;
  }

  query_ids_and_ts_from_user_with_tag(user, tag, params = []) {
    let query = this
      .select('m.id, UNIX_TIMESTAMP(m.published) as ts')
      .from('bm_marks as m, bm_marks_has_bm_tags as mht')
      .where('m.id = mht.mark_id')
      .and_where({ 'm.author': user.id, 'mht.tag_id': tag.id });
    if (!params.private) {
      query.and_where({ 'm.visibility': 0 });
    }
    if (db.driver() == 'sqlite') {
      query = query.select("m.id, strftime('%s', m.published) as ts");
    }
    return query;
  }

  query_ids_and_ts_from_friends(user, params = []) {
    const query = this.query_ids_and_ts({ visibility: 0, display: 1, author: user.following_ids });
    if (params.limit !== undefined) {
      query.limit(params.limit);
    }
    return query;
  }

  query_ids_and_ts_search(search, params) {
    const like = db.quote(`%${search}%`);
    const query = this
      .select('m.id, UNIX_TIMESTAMP(m.published) as ts')
      .from('bm_marks as m, bm_marks_has_bm_tags as mht')
      .where('m.id = mht.mark_id')
      .and_where(
        `(m.title LIKE ${like} OR m.content LIKE ${like} OR (mht.isHidden = 0 AND mht.label LIKE ${like}))`
      )
      .group_by('m.id');
    if (params.before) {
      const before = db.quote(params.before);
      query.and_where(`m.published <= FROM_UNIXTIME(${before})`);
    }
    return query;
  }

  query_ids_and_ts_search_public(search, params) {
    return this
      .query_ids_and_ts_search(search, params)
      .and_where({ 'm.visibility': 0, 'm.display': 1 });
  }

  query_ids_and_ts_from_user_search(user, search, params = []) {
    const query = this
      .query_ids_and_ts_search(search, params)
      .and_where({ 'm.author': user.id });
    if (!params.private) {
      query.and_where({ 'm.visibility': 0 });
    }
    return query;
  }
}

module.exports = marks;
