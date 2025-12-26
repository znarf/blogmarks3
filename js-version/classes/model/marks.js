class marks {
  create(user, params = {}) {
    const link = this.table('links').with_url(params.url);
    if (user.mark_with_link(link)) {
      throw new amateur.exception('Mark already exists.', 400);
    }
    const content = Michelf.Markdown.defaultTransform(params.description);
    const contentType = 'html';
    const mark = this.table('marks').create({
      author: user.id,
      related: link.id,
      title: params.title,
      visibility: params.visibility,
      content,
      contentType
    });
    this.table('marks_tags').tag_mark(
      mark,
      params.tags.split(','),
      params.private_tags.split(',')
    );
    this.table('screenshots').ensure_entry_exists_for_mark(mark);
    this.feed('marks').index(mark);
    this.search('marks').index(mark);
    return mark;
  }

  update(mark, params = {}) {
    let link;
    if (mark.url !== params.url) {
      link = this.table('links').with_url(params.url);
      if (mark.author.mark_with_link(link)) {
        throw new amateur.exception('Mark already exists.', 400);
      }
    } else {
      link = mark.related;
    }
    const content = Michelf.Markdown.defaultTransform(params.description);
    const contentType = 'html';
    const updatedMark = this.table('marks').update(mark, {
      related: link.id,
      title: params.title,
      visibility: params.visibility,
      content,
      contentType
    });
    this.feed('marks').unindex(updatedMark);
    this.table('marks_tags').tag_mark(
      updatedMark,
      params.tags.split(','),
      params.private_tags.split(',')
    );
    this.feed('marks').index(updatedMark);
    this.search('marks').index(updatedMark);
    return updatedMark;
  }

  delete(mark) {
    this.feed('marks').unindex(mark);
    this.search('marks').unindex(mark);
    this.table('marks_tags').delete({ mark_id: mark.id });
    this.table('marks').delete(mark);
  }

  delete_from_user(user) {
    const tag_ids = this
      .table('marks_tags')
      .select('DISTINCT tag_id as id')
      .where({ user_id: user.id })
      .fetch_ids();
    this.table('marks').delete({ author: user.id });
    this.table('marks_tags').delete({ user_id: user.id });
    const redis = this.service('redis').connection();
    if (redis) {
      redis.delete('feed_marks');
      redis.delete(`feed_marks_user_${user.id}`);
      redis.delete(`feed_marks_my_${user.id}`);
      for (const tag_id of tag_ids) {
        redis.delete(`feed_marks_tag_${tag_id}`);
        redis.delete(`feed_marks_my_${user.id}_tag_${tag_id}`);
      }
      redis.delete('tags_public');
      redis.delete(`tags_user_${user.id}_public`);
      redis.delete(`tags_user_${user.id}_private`);
    }
    this.search('marks').unindex_user(user);
  }

  search_with_query(query, params = {}) {
    const total = query.clone().select('COUNT(DISTINCT m.id)').group_by(null).count();
    const ids_and_ts = query
      .limit(params.limit + 1)
      .order_by('published DESC')
      .fetch_key_values('id', 'ts');

    let next = null;
    if (Object.keys(ids_and_ts).length > params.limit) {
      const keys = Object.keys(ids_and_ts);
      next = ids_and_ts[keys[keys.length - 1]];
      delete ids_and_ts[keys[keys.length - 1]];
    }

    const items = this.table('marks').get(Object.keys(ids_and_ts));
    return { params, total, next, items };
  }

  latests(params = {}) {
    const query = this.table('marks').query_latest_ids_and_ts;
    return this.feed('marks').query('feed_marks', query, params);
  }

  with_tag(tag, params = {}) {
    const query = this.table('marks').query_ids_and_ts_with_tag.__use(tag);
    return this.feed('marks').query(`feed_marks_tag_${tag.id}`, query, params);
  }

  with_tags(tags, params = {}) {
    if (this.search('marks').available()) {
      return this.search('marks').search({ tags, ...params });
    }
    let results;
    for (const tag of tags) {
      const query = this.table('marks').query_ids_and_ts_with_tag.__use(tag);
      const [tag_results] = this.feed('marks').ids_and_ts(null, query, { limit: -1, ...params });
      results = results ? array_intersect_key(results, tag_results) : tag_results;
    }
    const total = Object.keys(results).length;
    if (params.limit > 0) {
      results = array_slice(results, params.offset, params.limit + 1, true);
    }
    return this.feed('marks').prepare_items(results, total, params);
  }

  from_user(user, params = {}) {
    const query = this.table('marks').query_ids_and_ts_from_user.__use(user);
    return this.feed('marks').query(`feed_marks_user_${user.id}`, query, params);
  }

  from_user_with_tag(user, tag, params = {}) {
    if (this.search('marks').available()) {
      return this.search('marks').search({ user, tag, ...params });
    }
    const query = this.table('marks').query_ids_and_ts_from_user_with_tag.__use(user, tag, {
      private: false
    });
    return this.feed('marks').query(null, query, params);
  }

  from_user_with_tags(user, tags, params = {}) {
    if (this.search('marks').available()) {
      return this.search('marks').search({ user, tags, ...params });
    }
    let results;
    for (const tag of tags) {
      const query = this.table('marks').query_ids_and_ts_from_user_with_tag.__use(user, tag, {
        private: false
      });
      const [tag_results] = this.feed('marks').ids_and_ts(null, query, { limit: -1, ...params });
      results = results ? array_intersect_key(results, tag_results) : tag_results;
    }
    const total = Object.keys(results).length;
    if (params.limit > 0) {
      results = array_slice(results, params.offset, params.limit + 1, true);
    }
    return this.feed('marks').prepare_items(results, total, params);
  }

  private_from_user(user, params = {}) {
    const query = this.table('marks').query_ids_and_ts_from_user.__use(user, { private: true });
    return this.feed('marks').query(`feed_marks_my_${user.id}`, query, params);
  }

  private_from_user_with_tag(user, tag, params = {}) {
    const query = this.table('marks').query_ids_and_ts_from_user_with_tag.__use(user, tag, {
      private: true
    });
    return this.feed('marks').query(`feed_marks_my_${user.id}_tag_${tag.id}`, query, params);
  }

  private_from_user_with_tags(user, tags, params = {}) {
    if (this.search('marks').available()) {
      return this.search('marks').search({ user, tags, private: true, ...params });
    }
    let results;
    for (const tag of tags) {
      const query = this.table('marks').query_ids_and_ts_from_user_with_tag.__use(user, tag, {
        private: true
      });
      const feed_key = `feed_marks_my_${user.id}_tag_${tag.id}`;
      const [tag_results] = this.feed('marks').ids_and_ts(feed_key, query, { limit: -1, ...params });
      results = results ? array_intersect_key(results, tag_results) : tag_results;
    }
    const total = Object.keys(results).length;
    if (params.limit > 0) {
      results = array_slice(results, params.offset, params.limit + 1, true);
    }
    return this.feed('marks').prepare_items(results, total, params);
  }

  private_from_user_search(user, search, params = {}) {
    if (this.search('marks').available()) {
      return this.search('marks').search({ user, query: search, private: true, ...params });
    }
    const query = this
      .table('marks')
      .query_ids_and_ts_from_user_search(user, search, { ...params, private: true });
    return this.search_with_query(query, params);
  }

  public_search(search, params = {}) {
    if (this.search('marks').available()) {
      return this.search('marks').search({ query: search, ...params });
    }
    const query = this
      .table('marks')
      .query_ids_and_ts_search_public(search, params);
    return this.search_with_query(query, params);
  }

  from_friends(user, params = {}) {
    const query = this.table('marks').query_ids_and_ts_from_friends.__use(user, { limit: 1000 });
    return this.feed('marks').query(`feed_marks_friends_${user.id}`, query, params);
  }

  from_friends_with_tag(user, tag, params = {}) {
    return this.search('marks').search({ tag, user_ids: user.following_ids, ...params });
  }

  from_friends_with_tags(user, tags, params = {}) {
    return this.search('marks').search({ tags, user_ids: user.following_ids, ...params });
  }

  from_friends_search(user, query, params = {}) {
    return this.search('marks').search({ query, user_ids: user.following_ids, ...params });
  }
}

module.exports = marks;
