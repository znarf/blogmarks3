const base = require('../base');

class marks extends base {
  constructor() {
    super();
  }
  static default_params = {
    offset: 0,
    limit: 25,
    order: 'desc',
    after: '-inf',
    before: '+inf'
  };

  redis() {
    return this.service('redis').connection();
  }

  query(redis_key, query, params = {}) {
    const [results, total] = this.ids_and_ts(redis_key, query, params);
    return this.prepare_items(results, total, params);
  }

  ids_and_ts(redis_key, query, params = {}) {
    const redis = this.redis();
    params = { ...marks.default_params, ...params };
    let results;
    let total;

    if (!redis || !redis_key || !redis.exists(redis_key)) {
      if (typeof query === 'function') {
        query = query();
      }
      const order = params.order === 'asc' ? 'published ASC' : 'published DESC';
      results = query.order_by(order).fetch_key_values('id', 'ts');
      total = Object.keys(results).length;
      if (redis && redis_key) {
        register_shutdown_function(function () {
          Object.entries(results).forEach(([id, ts]) => redis.zAdd(redis_key, ts, id));
        });
      }
      if (params.before !== '+inf') {
        results = array_filter(results, function (ts) {
          return ts < params.before;
        });
      }
      if (params.after !== '-inf') {
        results = array_filter(results, function (ts) {
          return ts > params.after;
        });
      }
      if (params.limit > 0) {
        results = array_slice(results, params.offset, params.limit + 1, true);
      }
    } else {
      const options = { withscores: true };
      if (params.limit > 0) {
        options.limit = [params.offset, params.limit + 1];
      }
      total = redis.zCard(redis_key);
      if (total) {
        if (params.order === 'asc') {
          results = redis.zRangeByScore(
            redis_key,
            String(params.after),
            '(' + params.before,
            options
          );
        } else {
          results = redis.zRevRangeByScore(
            redis_key,
            String(params.before),
            '(' + params.after,
            options
          );
        }
      }
    }
    return [results, total];
  }

  prepare_items(results, total = null, params = {}) {
    const next = params.limit && Object.keys(results).length > params.limit
      ? parseInt(array_pop(results), 10)
      : null;
    const items = Object.keys(results).length === 0 ? [] : this.table('marks').get(Object.keys(results));
    return { params, total, next, items };
  }

  add(redis_key, ts, id) {
    const redis = this.redis();
    if (redis && redis.exists(redis_key)) {
      redis.zAdd(redis_key, ts, id);
    }
  }

  remove(redis_key, id) {
    const redis = this.redis();
    if (redis && redis.exists(redis_key)) {
      redis.zRem(redis_key, id);
    }
  }

  flush(redis_key) {
    const redis = this.redis();
    if (redis && redis.exists(redis_key)) {
      redis.delete(redis_key);
    }
  }

  index(mark) {
    const ts = mark.published.getTimestamp();
    if (mark.is_public) {
      this.add('feed_marks', ts, mark.id);
    }
    this.add(`feed_marks_my_${mark.author.id}`, ts, mark.id);
    if (mark.is_public) {
      this.add(`feed_marks_user_${mark.author.id}`, ts, mark.id);
    }
    for (const mt of mark.tags || []) {
      if (mark.is_public && !mt.isHidden) {
        this.add(`feed_marks_tag_${mt.tag_id}`, ts, mark.id);
      }
      this.add(`feed_marks_my_${mark.author.id}_tag_${mt.tag_id}`, ts, mark.id);
    }
    if (flag('enable_social_features')) {
      if (mark.is_public) {
        for (const user_id of mark.user.follower_ids) {
          this.add(`feed_marks_friends_${user_id}}`, ts, mark.id);
        }
      }
    }
  }

  unindex(mark) {
    this.remove('feed_marks', mark.id);
    this.remove(`feed_marks_my_${mark.author.id}`, mark.id);
    this.remove(`feed_marks_user_${mark.author.id}`, mark.id);
    for (const mt of mark.tags || []) {
      this.remove(`feed_marks_tag_${mt.tag_id}`, mark.id);
      this.remove(`feed_marks_my_${mark.author.id}_tag_${mt.tag_id}`, mark.id);
    }
    if (flag('enable_social_features')) {
      for (const user_id of mark.user.follower_ids) {
        this.remove(`feed_marks_friends_${user_id}}`, mark.id);
      }
    }
  }
}

module.exports = marks;
