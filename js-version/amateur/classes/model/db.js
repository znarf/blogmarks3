const path = require('path');
const Database = require('better-sqlite3');

let dbInstance = null;

function getDb() {
  if (!dbInstance) {
    const dbPath =
      process.env.BLOGMARKS_DB ||
      global.DB_PATH ||
      path.join(__dirname, '..', '..', '..', '..', 'blogmarks.sqlite');
    dbInstance = new Database(dbPath);
  }
  return dbInstance;
}

function formatDate(date) {
  const pad = (value) => String(value).padStart(2, '0');
  return (
    date.getFullYear() +
    '-' +
    pad(date.getMonth() + 1) +
    '-' +
    pad(date.getDate()) +
    ' ' +
    pad(date.getHours()) +
    ':' +
    pad(date.getMinutes()) +
    ':' +
    pad(date.getSeconds())
  );
}

function date(value) {
  const parsed = new Date(value);
  if (Number.isNaN(parsed.getTime())) {
    return value;
  }
  return formatDate(parsed);
}

function now() {
  return formatDate(new Date());
}

function insert_id() {
  const row = getDb().prepare('SELECT last_insert_rowid() as id').get();
  return row ? row.id : null;
}

module.exports = {
  getDb,
  date,
  now,
  insert_id
};
