class tags {
  constructor() {
    this.table = this.table('tags');
    this.feed = this.feed('tags');
  }

  latests(params = {}) {
    const query = this.table.query_latests;
    return this.feed.query('tags_public', query, params);
  }

  related_with(tag, params = {}) {
    const query = this.table.query_related_with.__use(tag);
    return this.feed.query(`tags_tag_${tag.id}_public`, query, params);
  }

  from_user(user, params = {}) {
    const query = this.table.query_from_user.__use(user, false);
    return this.feed.query(`tags_user_${user.id}_public`, query, params);
  }

  from_user_related_with(user, tag, params = {}) {
    const query = this.table.query_from_user_related_with.__use(user, tag, false);
    return this.feed.query(`tags_user_${user.id}_tag_${tag.id}_public`, query, params);
  }

  private_from_user(user, params = {}) {
    const query = this.table.query_from_user.__use(user, true);
    return this.feed.query(`tags_user_${user.id}_private`, query, params);
  }

  private_from_user_related_with(user, tag, params = {}) {
    const query = this.table.query_from_user_related_with.__use(user, tag, true);
    return this.feed.query(`tags_user_${user.id}_tag_${tag.id}_private`, query, params);
  }

  public_search(params = {}) {
    const merged = { limit: 100, ...params };
    const tagsList = this.latests({ limit: null, ...merged });
    return array_slice(tagsList, 0, merged.limit);
  }

  private_search_from_user(user, params = {}) {
    const merged = { limit: 100, ...params };
    const tagsList = this.private_from_user(user, { limit: null, ...merged });
    return array_slice(tagsList, 0, merged.limit);
  }
}

module.exports = tags;
