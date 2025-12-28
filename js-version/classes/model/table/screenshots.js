const table = require('../table');
const db = global.db;

class screenshots extends table {
  constructor() {
    super();
    this.tablename = 'bm_screenshots';
  }

  preload_for_marks(marks) {
    const filtered = marks.filter((mark) => !mark.attributes.screenshot);
    const link_ids = filtered.map((mark) => parseInt(mark.attributes.related, 10));
    if (!link_ids.length) {
      return;
    }
    const query = this
      .select(['link', 'url'])
      .where({ link: link_ids, status: 1 })
      .order_by('created');
    const results = query.fetch_key_values('link', 'url');
    filtered.forEach((mark) => {
      const link_id = mark.link_id();
      const screenshot = results[link_id] ? results[link_id] : mark.default_screenshot();
      mark.cache_attribute('screenshot', screenshot);
    });
  }

  for_mark(mark) {
    const query = this
      .select('url')
      .where({ link: mark.link_id(), status: 1 })
      .order_by('created DESC');
    return query.fetch_one();
  }

  ensure_entry_exists_for_mark(mark) {
    const now = db.now();
    const params = { link: mark.link_id() };
    const existing = this.where(params).and_where(`created > DATE_SUB('${now}', INTERVAL 1 DAY)`);
    if (existing.count() == 0) {
      const screenshot = this.create({ ...params, status: 0, created: now });
      this.service('amqp').push({ id: screenshot.id }, 'take_screenshot');
    }
  }
}

module.exports = screenshots;
