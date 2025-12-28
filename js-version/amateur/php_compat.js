const bcrypt = require('bcryptjs');
const Exception = require('./classes/exception');

function applyPhpCompat({ getCurrent, generateSessionId } = {}) {
  const currentAccessor = typeof getCurrent === 'function' ? getCurrent : () => null;


  global.class_exists = () => false;
  global.mb_detect_encoding = () => 'utf-8';
  global.utf8_encode = value => String(value);
  global.PASSWORD_DEFAULT = 'bcrypt';
  global.exception = Exception;
  global.password_hash = value => bcrypt.hashSync(String(value), 10);
  global.password_verify = (value, hash) => {
    try {
      return bcrypt.compareSync(String(value), String(hash));
    } catch (error) {
      return false;
    }
  };
  global.FILTER_VALIDATE_EMAIL = 'FILTER_VALIDATE_EMAIL';
  global.FILTER_VALIDATE_REGEXP = 'FILTER_VALIDATE_REGEXP';
  global.filter_var = (value, filter, options = {}) => {
    if (filter === global.FILTER_VALIDATE_EMAIL) {
      const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return pattern.test(String(value)) ? value : false;
    }
    if (filter === global.FILTER_VALIDATE_REGEXP) {
      const regex = options.options && options.options.regexp ? options.options.regexp : null;
      if (!regex) {
        return false;
      }
      return regex.test(String(value)) ? value : false;
    }
    return value;
  };
  global.db = {
    driver: () => 'sqlite',
    now: () => new Date().toISOString(),
    quote: value => `'${String(value).replace(/'/g, "''")}'`,
    fetch_assoc: () => null,
  };
  global.cache = {
    _store: new Map(),
    get(key) {
      return this._store.get(key);
    },
    set(key, value) {
      this._store.set(key, value);
      return value;
    },
    preload() {
      return true;
    },
    loaded(key) {
      return this._store.has(key);
    },
  };

  global.time = () => Math.floor(Date.now() / 1000);
  global.strftime = (format, timestamp = time()) => {
    const date = new Date(timestamp * 1000);
    const months = [
      'January',
      'February',
      'March',
      'April',
      'May',
      'June',
      'July',
      'August',
      'September',
      'October',
      'November',
      'December',
    ];
    const day = String(date.getUTCDate()).padStart(2, '0');
    const month = months[date.getUTCMonth()];
    const year = date.getUTCFullYear();
    const hour = String(date.getUTCHours()).padStart(2, '0');
    if (format === '%B %Y') {
      return `${month} ${year}`;
    }
    if (format === '%d %B %Y') {
      return `${day} ${month} ${year}`;
    }
    if (format === '%d %B %Y %H:00') {
      return `${day} ${month} ${year} ${hour}:00`;
    }
    return date.toISOString();
  };

  const datetimeConstants = {
    RFC3339: 'RFC3339',
    W3C: 'W3C',
  };
  global.datetimezone = class DateTimeZone {
    constructor(name) {
      this.name = name;
    }
  };
  const DateTime = class DateTime {
    constructor(value) {
      this.date = value ? new Date(value) : new Date();
    }
    format(fmt) {
      if (fmt === datetimeConstants.RFC3339 || fmt === datetimeConstants.W3C) {
        return this.date.toISOString();
      }
      if (fmt === 'Y') {
        return String(this.date.getUTCFullYear());
      }
      return this.date.toISOString();
    }
    getTimestamp() {
      return Math.floor(this.date.getTime() / 1000);
    }
  };
  Object.assign(DateTime, datetimeConstants);
  global.datetime = DateTime;

  global.Michelf = {
    Markdown: {
      defaultTransform(text) {
        return text || '';
      },
    },
  };
  global.Markdownify = {
    Converter: class Converter {
      parseString(value) {
        return value || '';
      }
    },
  };

}

module.exports = applyPhpCompat;
