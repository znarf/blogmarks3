const base = require('../base');

class marks extends base {
  constructor() {
    super();
    this.documents = [];
  }

  to_array(mark) {
    return {
      id: parseInt(mark.id, 10),
      created_at: mark.published.format(datetime.RFC3339),
      updated_at: mark.updated.format(datetime.RFC3339),
      user_id: mark.user_id(),
      link_id: mark.link_id(),
      url: this.fix_encoding(mark.url),
      title: this.fix_encoding(mark.title),
      content_type: mark.contentType,
      content:
        mark.contentType === 'html'
          ? strip_tags(this.fix_encoding(mark.content))
          : this.fix_encoding(mark.content),
      public: mark.is_public,
      private: mark.is_private,
      tags: Object.values(mark.public_tags.map((tag) => this.convert_tag(tag))),
      private_tags: Object.values(mark.private_tags.map((tag) => this.convert_tag(tag)))
    };
  }

  fix_encoding(string) {
    const encoding = mb_detect_encoding(string, 'auto', true);
    if (!encoding) {
      return utf8_encode(string);
    }
    return string;
  }

  convert_tag(tag) {
    let string = String(tag);
    const encoding = mb_detect_encoding(string, 'auto', true);
    if (!encoding) {
      string = utf8_encode(string);
    }
    return string;
  }

  available() {
    return this.service('search').client();
  }

  asynchronous(async = true) {
    return async && this.service('amqp').channel();
  }

  index(mark, async = true) {
    if (this.asynchronous(async)) {
      this.service('amqp').push({ action: 'index', mark_id: mark.id }, 'marks-index');
    } else if (this.available()) {
      this.documents.push(new Document(mark.id, this.to_array(mark)));
      if (this.documents.length >= 100) {
        this.flush_index_buffer();
      }
    }
  }

  flush_index_buffer() {
    if (this.documents.length) {
      const client = this.service('search').client();
      if (client) {
        try {
          client.getindex('bm').gettype('marks').adddocuments(this.documents);
        } catch (e) {
          error_log(e.getMessage());
        }
      }
      this.documents = [];
    }
  }

  unindex(mark, async = true) {
    if (this.asynchronous(async)) {
      this.service('amqp').push({ action: 'unindex', mark_id: mark.id }, 'marks-index');
    } else if (this.available()) {
      this.service('search').delete('/bm/marks/' + mark.id);
    }
  }

  index_user(user, async = true) {
    if (this.asynchronous(async)) {
      this.service('amqp').push({ action: 'index_user', user_id: user.id }, 'marks-index');
    } else {
      const marks = this.model('marks').private_marks_from_user(user, { limit: -1 });
      for (const mark of marks.items) {
        this.index(mark, false);
      }
    }
  }

  unindex_user(user, async = true) {
    if (this.asynchronous(async)) {
      this.service('amqp').push({ action: 'unindex_user', user_id: user.id }, 'marks-index');
    } else if (this.available()) {
      this.service('search').delete('/bm/marks/_query?q=user_id:' + user.id);
    }
  }

  build_base_query(params) {
    const query = { query: { filtered: { query: {}, filter: { and: [] } } } };

    if (!params.query) {
      query.query.filtered.query = { match_all: {} };
    } else {
      query.query.filtered.query = {
        multi_match: {
          query: params.query,
          fields: ['title^2', 'url', 'content', 'tags.partial']
        }
      };
      if (params.private) {
        query.query.filtered.query.multi_match.fields.push('private_tags.partial');
      }
    }

    if (params.user) {
      const user = params.user;
      query.query.filtered.filter.and.push({ term: { user_id: user.id } });
    }
    if (params.tag) {
      const tag = params.tag;
      query.query.filtered.filter.and.push({ term: { tags: String(tag) } });
    }
    if (params.tags) {
      for (const tag of params.tags) {
        query.query.filtered.filter.and.push({ term: { tags: String(tag) } });
      }
    }
    if (!params.private) {
      query.query.filtered.filter.and.push({ term: { private: false } });
    }

    if (params.user_ids) {
      const or = [];
      for (const user_id of params.user_ids) {
        or.push({ term: { user_id } });
      }
      query.query.filtered.filter.and.push({ or });
    }

    return query;
  }

  build_full_query(params, query = {}) {
    const order = params.order === 'asc' ? 'asc' : 'desc';

    query.sort = { created_at: { order } };

    if (params.before) {
      const before = date(datetime.RFC3339, params.before - 1);
      query.query.filtered.filter.and.push({ range: { created_at: { to: before } } });
    }
    if (params.after) {
      const after = date(datetime.RFC3339, params.after);
      query.query.filtered.filter.and.push({ range: { created_at: { from: after } } });
    }

    if (params.limit !== undefined) {
      query.size = params.limit + 1;
      if (params.offset !== undefined) {
        query.from = params.offset;
      }
    }

    return query;
  }

  search(params = []) {
    if (!this.available()) {
      throw new amateur.exception('Search backend not available.', 500);
    }
    let query = this.build_base_query(params);
    let result = this.service('search').count('/bm/marks', query);
    const total = parseInt(result.count, 10);
    query = this.build_full_query(params, query);
    result = this.service('search').search('/bm/marks', query);
    let next;
    if (result.hits.hits.length > params.limit) {
      const hit = result.hits.hits.pop();
      next = strtotime(hit._source.created_at);
    }
    const ids = result.hits.hits.map((hit) => parseInt(hit._id, 10));
    const items = this.table('marks').get(ids);
    return { params, total, next, items };
  }
}

module.exports = marks;
