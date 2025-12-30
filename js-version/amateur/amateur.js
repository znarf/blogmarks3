const querystring = require('querystring');
const path = require('path');
const Replaceable = require('./classes/replaceable');
const BaseQuery = require('./classes/model/query');
const BaseTable = require('./classes/model/table');
const BaseResource = require('./classes/model/resource');
const Cache = require('./classes/model/cache');
const db = require('./classes/model/db');
const Exception = require('./classes/exception');
const { session_cookie, parse_cookies, resolve_session_data, sync_session_cookie } = require('./classes/session');

const registry = {
  actions: {},
  views: {},
  layouts: {},
  partials: {},
  renders: {},
  helpers: {},
  content: '',
  layout_output: '',
};

registry.reset = function () {
  registry.content = '';
  registry.layout_output = '';
};

let current = null;

function sendResponse() {
  const { code, headers, body } = current.response;
  current.res.writeHead(code, headers);
  current.res.end(body);
}

function runHandler(handler) {
  let result;
  try {
    result = handler();
  } catch (error) {
    if (!error || !error.silent) {
      throw error;
    }
    result = current.response.body;
  }
  if (result !== undefined) {
    Replaceable.call('response_content', [result]);
  }
  return result;
}

function handleRequest(handler, req, res, options = {}) {
  const url = new URL(req.url, `http://${req.headers.host || 'localhost'}`);
  const query = Object.fromEntries(url.searchParams.entries());
  const bodyParams = options.express && req.body && !Buffer.isBuffer(req.body) ? req.body || {} : {};
  const cookies = parse_cookies(req.headers.cookie || '');
  const sessionCookie = cookies[session_cookie()];
  const sessionData = resolve_session_data(sessionCookie);

  current = {
    req,
    res,
    pathname: url.pathname,
    search: url.search,
    method: req.method,
    params: { ...query, ...bodyParams },
    session: sessionData,
    session_id: sessionCookie || null,
    body: options.express ? req.body || {} : null,
    response: {
      code: 200,
      headers: { 'Content-Type': 'text/html; charset=utf-8' },
      body: '',
    },
  };
  current.files = {};
  if (options.express && Array.isArray(req.files)) {
    req.files.forEach((file) => {
      current.files[file.fieldname] = {
        name: file.originalname || '',
        type: file.mimetype || '',
        tmp_name: file.path || '',
        error: 0,
        size: file.size || 0,
      };
    });
  }
  if (global.__amateur_state) {
    global.__amateur_state.current = current;
  }

  if (module.exports.registry && typeof module.exports.registry.reset === 'function') {
    module.exports.registry.reset();
  }
  if (global.blogmarks && global.blogmarks.registry && typeof global.blogmarks.registry.reset === 'function') {
    global.blogmarks.registry.reset();
  }
  global.SESSION = current.session;

  if (options.express) {
    runHandler(handler);
    sync_session_cookie(current, sessionCookie);
    return sendResponse();
  }

  const collectBody = ['POST', 'PUT', 'PATCH', 'DELETE'].includes(req.method);
  if (!collectBody) {
    runHandler(handler);
    sync_session_cookie(current, sessionCookie);
    return sendResponse();
  }

  let raw = '';
  req.on('data', (chunk) => {
    raw += chunk;
  });
  req.on('end', () => {
    current.body = raw;
    const contentType = req.headers['content-type'] || '';
    if (contentType.includes('application/x-www-form-urlencoded')) {
      current.params = { ...current.params, ...querystring.parse(raw) };
    }
    runHandler(handler);
    sync_session_cookie(current, sessionCookie);
    sendResponse();
  });
}

function runOnce(handler, options = {}) {
  const urlValue = options.url || '/';
  const method = options.method || 'GET';
  const headers = options.headers || {};
  const url = new URL(urlValue, `http://${headers.host || 'localhost'}`);
  const query = Object.fromEntries(url.searchParams.entries());
  const cookies = parse_cookies(headers.cookie || '');
  const sessionCookie = cookies[session_cookie()];
  const sessionData = resolve_session_data(sessionCookie);

  current = {
    req: { url: urlValue, method, headers },
    res: { writeHead: () => {}, end: () => {} },
    pathname: url.pathname,
    search: url.search,
    method,
    params: { ...query, ...(options.params || {}) },
    session: sessionData,
    session_id: sessionCookie || null,
    body: options.body || '',
    response: {
      code: 200,
      headers: { 'Content-Type': 'text/html; charset=utf-8' },
      body: '',
    },
  };
  current.files = options.files || {};
  if (global.__amateur_state) {
    global.__amateur_state.current = current;
  }
  global.SESSION = current.session;
  const contentType = headers['content-type'] || '';
  if (contentType.includes('application/x-www-form-urlencoded')) {
    current.params = { ...current.params, ...querystring.parse(options.body || '') };
  }

  runHandler(handler);
  sync_session_cookie(current, sessionCookie);
  return current.response;
}

function initGlobals() {
  global._ = (value) => value;
  global.amateur = module.exports;
  global.__amateur_state = { registry, current: null };

  global.exception = Exception;
  global.db = db;
  global.cache = new Cache();

  Replaceable.load_replaceables(path.join(__dirname, 'replaceables'));
}

module.exports = {
  model: {
    table: BaseTable,
    resource: BaseResource,
    query: BaseQuery,
    db,
  },
  registry,
  runOnce,
  handleRequest,
  initGlobals,
  Replaceable,
};
