const { getDb } = require('./db');

class BaseQuery {
  constructor(table) {
    this._select = '*';
    this._from = table;
    this._where = [];
    this._params = [];
    this._groupBy = null;
    this._orderBy = null;
    this._limit = null;
    this._offset = null;
    this._having = null;
  }

  select(fields) {
    if (Array.isArray(fields)) {
      this._select = fields.join(', ');
    } else {
      this._select = fields;
    }
    return this;
  }

  from(table) {
    this._from = table;
    return this;
  }

  _addWhere(condition) {
    if (!condition) {
      return this;
    }
    if (typeof condition === 'string') {
      this._where.push(condition);
      return this;
    }
    Object.entries(condition).forEach(([key, value]) => {
      if (Array.isArray(value)) {
        const placeholders = value.map(() => '?').join(', ');
        this._where.push(`${key} IN (${placeholders})`);
        this._params.push(...value);
      } else {
        this._where.push(`${key} = ?`);
        this._params.push(value);
      }
    });
    return this;
  }

  where(condition) {
    return this._addWhere(condition);
  }

  and_where(condition) {
    return this._addWhere(condition);
  }

  group_by(value) {
    this._groupBy = value;
    return this;
  }

  order_by(value) {
    this._orderBy = value;
    return this;
  }

  limit(value) {
    this._limit = value;
    return this;
  }

  having(value) {
    this._having = value;
    return this;
  }

  _sql() {
    let sql = `SELECT ${this._select} FROM ${this._from}`;
    if (this._where.length) {
      sql += ` WHERE ${this._where.join(' AND ')}`;
    }
    if (this._groupBy) {
      sql += ` GROUP BY ${this._groupBy}`;
    }
    if (this._having) {
      sql += ` HAVING ${this._having}`;
    }
    if (this._orderBy) {
      sql += ` ORDER BY ${this._orderBy}`;
    }
    if (this._limit !== null && this._limit !== undefined) {
      sql += ` LIMIT ${this._limit}`;
      if (this._offset !== null && this._offset !== undefined) {
        sql += ` OFFSET ${this._offset}`;
      }
    }
    return sql;
  }

  execute() {
    const stmt = getDb().prepare(this._sql());
    return stmt.all(this._params);
  }

  fetch_key_values(keyField, valueField) {
    const rows = this.execute();
    const result = {};
    rows.forEach((row) => {
      result[row[keyField]] = row[valueField];
    });
    return result;
  }

  fetch_ids(field = 'id') {
    const rows = this.execute();
    return rows.map((row) => row[field]);
  }

  fetch_one() {
    const stmt = getDb().prepare(this._sql());
    return stmt.get(this._params);
  }

  count() {
    const row = this.fetch_one();
    if (!row) {
      return 0;
    }
    const firstKey = Object.keys(row)[0];
    return row[firstKey];
  }
}

module.exports = BaseQuery;
