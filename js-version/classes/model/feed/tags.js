const base = require('../base');
const Tag = require('../resource/tag');

class tags extends base {
  constructor() {
    super();
  }
  redis() {
    return this.service('redis').connection();
  }

  query(redis_key, query, params = {}) {
    const redis = this.redis();
    params = { offset: 0, limit: 50, ...params };
    let results;

    if (!redis || !redis_key || !redis.exists(redis_key)) {
      if (typeof query === 'function') {
        query = query();
      }
      results = query.order_by('count DESC').fetch_key_values('label', 'count');
      if (redis && redis_key) {
        register_shutdown_function(function () {
          Object.entries(results).forEach(([label, count]) => redis.zAdd(redis_key, count, label));
        });
      }
      if (params.limit) {
        results = Object.fromEntries(
          Object.entries(results).slice(params.offset, params.offset + params.limit)
        );
      }
    } else {
      const options = { withscores: true };
      if (params.limit) {
        options.limit = [params.offset, params.limit];
      }
      results = redis.zRevRangeByScore(redis_key, '+inf', 1, options);
    }

    const tags = [];
    Object.entries(results).forEach(([label, count]) => {
      if (!params.query) {
        tags.push(new Tag({ label, count }));
      } else {
        params.query.split(' ').forEach(token => {
          if (label.toLowerCase().indexOf(token.toLowerCase()) !== -1) {
            tags.push(new Tag({ label, count }));
          }
        });
      }
    });
    return tags;
  }
}

module.exports = tags;
