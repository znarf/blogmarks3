const table = require('../table');
const db = global.db;

class marks_tags extends table {
  constructor() {
    super();
    this.classname = '\\blogmarks\\model\\resource\\tag';
    this.tablename = 'bm_marks_has_bm_tags';
  }

  from_mark(mark) {
    const cache_key = `bm_marks_tags_id_${mark.id}`;
    let rows = cache.get(cache_key);
    if (!Array.isArray(rows)) {
      rows = this.fetch_all({ mark_id: mark.id });
      cache.set(cache_key, rows);
    }
    return rows.map((row) => this._to_object(row));
  }

  tag_mark(mark, tags = [], private_tags = []) {
    this.delete({ mark_id: mark.id });
    const rows = [];
    const objects = [];
    const types = { tags: 0, private_tags: 1 };
    Object.entries(types).forEach(([type, is_hidden]) => {
      const list = type === 'tags' ? tags : private_tags;
      list.forEach((tagValue) => {
        const trimmed = String(tagValue).trim();
        if (!trimmed) {
          return;
        }
        const tag = this.table('tags').with_label(trimmed);
        const row = {
          mark_id: mark.id,
          tag_id: tag.id,
          user_id: mark.author.id,
          link_id: mark.related.id,
          label: tag.label,
          isHidden: is_hidden,
          visibility: mark.visibility
        };
        rows.push(row);
        objects.push(this.create(row));
      });
    });
    const cache_key = `bm_marks_tags_id_${mark.id}`;
    cache.set(cache_key, rows);
    return objects;
  }

  preload_for_mark_ids(ids) {
    const filtered = ids.filter((id) => !cache.loaded(`bm_marks_tags_id_${id}`));
    if (!filtered.length) {
      return;
    }
    for (let i = 0; i < filtered.length; i += 1000) {
      const ids_chunk = filtered.slice(i, i + 1000);
      const result = this
        .select(['mark_id', 'tag_id', 'label', 'isHidden'])
        .where({ mark_id: ids_chunk })
        .execute();
      const results = {};
      let row;
      while ((row = db.fetch_assoc(result))) {
        const id = parseInt(row.mark_id, 10);
        delete row.mark_id;
        if (!results[id]) {
          results[id] = [];
        }
        results[id].push(row);
      }
      ids_chunk.forEach((id) => {
        cache.set(`bm_marks_tags_id_${id}`, results[id] ? results[id] : []);
      });
    }
  }
}

module.exports = marks_tags;
