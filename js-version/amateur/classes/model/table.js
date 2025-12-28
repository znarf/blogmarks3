const path = require('path');
const { getDb } = require('./db');
const BaseQuery = require('./query');

class InsertBuilder {
  constructor(table, columns = null) {
    this.table = table;
    this.columns = columns;
    this.values = [];
    this._set = null;
  }

  set(params) {
    this._set = params;
    return this;
  }

  execute() {
    if (this._set) {
      const keys = Object.keys(this._set);
      const placeholders = keys.map(() => '?').join(', ');
      const sql = `INSERT INTO ${this.table} (${keys.join(', ')}) VALUES (${placeholders})`;
      const stmt = getDb().prepare(sql);
      return stmt.run(Object.values(this._set));
    }
    if (!this.columns || this.values.length === 0) {
      return { changes: 0 };
    }
    const rowPlaceholders = this.columns.map(() => '?').join(', ');
    const placeholders = this.values.map(() => `(${rowPlaceholders})`).join(', ');
    const sql = `INSERT INTO ${this.table} (${this.columns.join(', ')}) VALUES ${placeholders}`;
    const stmt = getDb().prepare(sql);
    const flatValues = this.values.reduce((acc, row) => acc.concat(row), []);
    return stmt.run(flatValues);
  }
}

class BaseTable {
  constructor() {
    this.classname = null;
    this.tablename = null;
    const registry = require(path.join(__dirname, '..', '..', '..', 'classes', 'registry'));
    this.table = registry.table.bind(registry);
    this.model = registry.model.bind(registry);
    this.service = registry.service.bind(registry);
    this.feed = registry.feed.bind(registry);
    this.search = registry.search.bind(registry);
  }

  _resolveClass() {
    if (!this.classname) {
      return null;
    }
    const cleaned = String(this.classname).replace(/^\\+/, '');
    let parts = cleaned.split('\\').map((part) => part.toLowerCase());
    if (parts[0] === 'blogmarks') {
      parts = parts.slice(1);
    }
    const filePath = path.join(__dirname, '..', '..', '..', 'classes', ...parts) + '.js';
    return require(filePath);
  }

  _decorate(instance, row) {
    if (!instance || !row) {
      return instance;
    }
    Object.assign(instance, row);
    instance.attributes = row;
    if (instance.table === undefined) {
      const registry = require(path.join(__dirname, '..', '..', '..', 'classes', 'registry'));
      instance.table = registry.table.bind(registry);
      instance.model = registry.model.bind(registry);
      instance.service = registry.service.bind(registry);
      instance.feed = registry.feed.bind(registry);
      instance.search = registry.search.bind(registry);
    }
    const proto = Object.getPrototypeOf(instance);
    const computed = [
      'user',
      'author',
      'related',
      'url',
      'screenshot',
      'tags',
      'text',
      'public_tags',
      'private_tags',
      'published',
      'updated',
      'name',
      'avatar',
      'following_ids',
      'follower_ids',
      'username'
    ];
    computed.forEach((name) => {
      const method = proto && typeof proto[name] === 'function' ? proto[name] : null;
      if (method) {
        try {
          instance[name] = method.call(instance);
        } catch (error) {
          return;
        }
      }
    });
    return instance;
  }

  _to_object(row) {
    if (!row) {
      return null;
    }
    const ResourceClass = this._resolveClass();
    if (ResourceClass) {
      const instance = new ResourceClass(row);
      return this._decorate(instance, row);
    }
    return row;
  }

  create(params = {}) {
    const result = this.insert().set(params).execute();
    const id = result.lastInsertRowid;
    return id ? this.get_one('id', id) : params;
  }

  update(target, params = {}) {
    const where = typeof target === 'object' && target.id ? { id: target.id } : target;
    const keys = Object.keys(params);
    const sets = keys.map((key) => `${key} = ?`).join(', ');
    const { clause, values } = this._buildWhere(where);
    const sql = `UPDATE ${this.tablename} SET ${sets} ${clause}`;
    getDb()
      .prepare(sql)
      .run([...keys.map((key) => params[key]), ...values]);
    if (typeof target === 'object' && target.id) {
      return this.get_one('id', target.id);
    }
    return target;
  }

  delete(target) {
    if (typeof target === 'object' && target.id) {
      const sql = `DELETE FROM ${this.tablename} WHERE id = ?`;
      return getDb().prepare(sql).run(target.id);
    }
    const { clause, values } = this._buildWhere(target);
    const sql = `DELETE FROM ${this.tablename} ${clause}`;
    return getDb().prepare(sql).run(values);
  }

  _buildWhere(condition) {
    if (!condition) {
      return { clause: '', values: [] };
    }
    if (typeof condition === 'string') {
      return { clause: `WHERE ${condition}`, values: [] };
    }
    const clauses = [];
    const values = [];
    Object.entries(condition).forEach(([key, value]) => {
      if (Array.isArray(value)) {
        const placeholders = value.map(() => '?').join(', ');
        clauses.push(`${key} IN (${placeholders})`);
        values.push(...value);
      } else {
        clauses.push(`${key} = ?`);
        values.push(value);
      }
    });
    return { clause: `WHERE ${clauses.join(' AND ')}`, values };
  }

  get_one(field, value) {
    const sql = `SELECT * FROM ${this.tablename} WHERE ${field} = ? LIMIT 1`;
    const row = getDb().prepare(sql).get(value);
    return this._to_object(row);
  }

  get_all(field, values) {
    if (!Array.isArray(values) || values.length === 0) {
      return [];
    }
    const placeholders = values.map(() => '?').join(', ');
    const sql = `SELECT * FROM ${this.tablename} WHERE ${field} IN (${placeholders})`;
    const rows = getDb().prepare(sql).all(values);
    return rows.map((row) => this._to_object(row));
  }

  get(arg) {
    if (Array.isArray(arg)) {
      return this.get_all('id', arg);
    }
    return this.get_one('id', arg);
  }

  fetch_all(where = {}) {
    const { clause, values } = this._buildWhere(where);
    const sql = `SELECT * FROM ${this.tablename} ${clause}`;
    const rows = getDb().prepare(sql).all(values);
    return rows.map((row) => this._to_object(row));
  }

  fetch_object(where = {}) {
    const { clause, values } = this._buildWhere(where);
    const sql = `SELECT * FROM ${this.tablename} ${clause} LIMIT 1`;
    const row = getDb().prepare(sql).get(values);
    return this._to_object(row);
  }

  select(fields) {
    const query = new BaseQuery(this.tablename);
    if (fields) {
      query.select(fields);
    }
    return query;
  }

  where(condition) {
    return new BaseQuery(this.tablename).where(condition);
  }

  insert(columns = null) {
    return new InsertBuilder(this.tablename, columns);
  }

  cache_key(key, value, type = 'raw') {
    return `cache_${this.tablename}_${type}_${key}_${value}`;
  }

  preload() {
    return true;
  }
}

module.exports = BaseTable;
