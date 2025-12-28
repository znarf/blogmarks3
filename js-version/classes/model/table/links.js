const table = require('../table');

class links extends table {
  constructor() {
    super();
    this.tablename = 'bm_links';
    this.unique_indexes = ['id', 'href'];
  }

  cache_key(key, value, type = 'raw') {
    const hashed = key === 'href' ? md5(value) : value;
    return super.cache_key(key, hashed, type);
  }

  with_url(url) {
    return this.get_one('href', url) || this.create({ href: url });
  }

  preload_for_marks(marks) {
    const filtered = marks.filter((mark) => !mark.attributes.url);
    const link_ids = filtered.map((mark) => parseInt(mark.attributes.related, 10));
    if (link_ids.length) {
      this.get(link_ids);
    }
  }
}

module.exports = links;
