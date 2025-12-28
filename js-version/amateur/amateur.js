const nodeCrypto = require('node:crypto');

const querystring = require('querystring');
const fs = require('fs');
const path = require('path');
const Amateur = require('./classes/amateur');
const BaseQuery = require('./classes/model/query');
const BaseTable = require('./classes/model/table');
const BaseResource = require('./classes/model/resource');
const db = require('./classes/model/db');
const applyPhpCompat = require('./php_compat');
const SESSION_COOKIE = 'bm_session';
const sessionStore = new Map();
const SESSION_SECRET = process.env.SESSION_SECRET || '';

function signSessionPayload(payload) {
  if (!SESSION_SECRET) {
    return '';
  }
  return nodeCrypto.createHmac('sha256', SESSION_SECRET).update(payload).digest('hex');
}

function parseSessionCookie(value) {
  if (!SESSION_SECRET || !value) {
    return null;
  }
  const [payload, sig] = value.split('.');
  if (!payload || !sig) {
    return null;
  }
  const expected = signSessionPayload(payload);
  if (!expected || expected.length !== sig.length) {
    return null;
  }
  const valid = nodeCrypto.timingSafeEqual(Buffer.from(expected), Buffer.from(sig));
  if (!valid) {
    return null;
  }
  try {
    const json = Buffer.from(payload, 'base64').toString('utf8');
    return JSON.parse(json);
  } catch (error) {
    return null;
  }
}

function serializeSessionCookie(data) {
  if (!SESSION_SECRET) {
    return null;
  }
  const json = JSON.stringify(data || {});
  const payload = Buffer.from(json, 'utf8').toString('base64');
  const sig = signSessionPayload(payload);
  if (!sig) {
    return null;
  }
  return `${payload}.${sig}`;
}

function parseCookies(header = '') {
  const cookies = {};
  header.split(';').forEach(pair => {
    const trimmed = pair.trim();
    if (!trimmed) {
      return;
    }
    const [key, ...rest] = trimmed.split('=');
    cookies[key] = decodeURIComponent(rest.join('='));
  });
  return cookies;
}

function parseMultipart(buffer, contentType) {
  const match = contentType.match(/boundary=([^;]+)/i);
  if (!match) {
    return { fields: {}, files: {} };
  }
  const boundary = '--' + match[1];
  const body = buffer.toString('latin1');
  const parts = body.split(boundary).slice(1, -1);
  const fields = {};
  const files = {};
  parts.forEach(part => {
    let chunk = part;
    if (chunk.startsWith('\r\n')) {
      chunk = chunk.slice(2);
    }
    if (chunk.endsWith('\r\n')) {
      chunk = chunk.slice(0, -2);
    }
    const [rawHeaders, ...bodyParts] = chunk.split('\r\n\r\n');
    const bodyContent = bodyParts.join('\r\n\r\n');
    const headers = rawHeaders.split('\r\n');
    const disposition = headers.find(line => line.toLowerCase().startsWith('content-disposition'));
    if (!disposition) {
      return;
    }
    const nameMatch = disposition.match(/name=\"([^\"]+)\"/i);
    if (!nameMatch) {
      return;
    }
    const fieldName = nameMatch[1];
    const filenameMatch = disposition.match(/filename=\"([^\"]*)\"/i);
    const typeHeader = headers.find(line => line.toLowerCase().startsWith('content-type'));
    if (filenameMatch && filenameMatch[1]) {
      const filename = filenameMatch[1];
      const tmpName = path.join('/tmp', `${Date.now()}_${Math.random().toString(16).slice(2)}`);
      const fileBuffer = Buffer.from(bodyContent, 'latin1');
      fs.writeFileSync(tmpName, fileBuffer);
      files[fieldName] = {
        name: filename,
        type: typeHeader ? typeHeader.split(':')[1].trim() : '',
        tmp_name: tmpName,
        error: 0,
        size: fileBuffer.length,
      };
    } else {
      fields[fieldName] = bodyContent.replace(/\r\n$/, '');
    }
  });
  return { fields, files };
}

function syncSessionCookie(sessionCookie) {
  if (!SESSION_SECRET) {
    return;
  }
  const payload = serializeSessionCookie(current.session);
  if (!payload) {
    return;
  }
  const cookie = `${SESSION_COOKIE}=${encodeURIComponent(payload)}; Path=/; HttpOnly`;
  if (current.response.headers['Set-Cookie'] !== cookie) {
    current.response.headers['Set-Cookie'] = cookie;
  }
}

const registry = {
  replaceables: {},
  actions: {},
  views: {},
  layouts: {},
  partials: {},
  renders: {},
  helpers: {},
  content: '',
  layout_output: '',
  expose: false,
};

const paths = {
  root: '',
  replaceables: '',
  views: '',
  layouts: '',
  partials: '',
  renders: '',
  modules: '',
  helpers: '',
  actions: '',
  public: '',
};

let current = null;

function generateSessionId() {
  return Math.random().toString(36).slice(2) + Date.now().toString(36);
}

function setPaths(nextPaths) {
  Object.assign(paths, nextPaths);
}

function createGlobalReplaceable(name) {
  global[name] = (...args) => registry.replaceables[name](...args);
}

function replaceable(name, fn) {
  if (typeof fn === 'function') {
    registry.replaceables[name] = fn;
    if (registry.expose) {
      createGlobalReplaceable(name);
    }
    return fn;
  }
  return registry.replaceables[name];
}

function callReplaceable(name, ...args) {
  return replaceable(name)(...args);
}

function loadReplaceables(dir) {
  const entries = fs.readdirSync(dir, { withFileTypes: true });
  entries.forEach(entry => {
    const fullPath = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      loadReplaceables(fullPath);
      return;
    }
    if (entry.isFile() && entry.name.endsWith('.js')) {
      const name = path.basename(entry.name, '.js');
      const exported = require(fullPath);
      if (typeof exported === 'function') {
        replaceable(name, exported);
      }
    }
  });
}

function include(filePath, args = {}) {
  const exported = require(filePath);
  if (typeof exported === 'function') {
    return exported(args);
  }
  return exported;
}

function sendResponse() {
  const { code, headers, body } = current.response;
  current.res.writeHead(code, headers);
  current.res.end(body);
}

function handleRequest(handler, req, res, options = {}) {
  const url = new URL(req.url, `http://${req.headers.host || 'localhost'}`);
  const query = Object.fromEntries(url.searchParams.entries());
  const bodyParams = options.express && req.body && !Buffer.isBuffer(req.body) ? req.body || {} : {};
  const cookies = parseCookies(req.headers.cookie || '');
  const sessionCookie = cookies[SESSION_COOKIE];
  let sessionData = {};
  if (SESSION_SECRET && sessionCookie) {
    if (sessionStore.has(sessionCookie)) {
      sessionData = sessionStore.get(sessionCookie);
    } else {
      const parsed = parseSessionCookie(sessionCookie);
      sessionData = parsed || {};
      sessionStore.set(sessionCookie, sessionData);
    }
  }

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
  if (req.headers['content-type'] && req.headers['content-type'].includes('multipart/form-data')) {
    const buffer = Buffer.isBuffer(req.body) ? req.body : Buffer.from(current.body || '', 'latin1');
    const parsed = parseMultipart(buffer, req.headers['content-type']);
    current.params = { ...current.params, ...parsed.fields };
    global.FILES = parsed.files;
  } else {
    global.FILES = {};
  }
  if (global.__amateur_state) {
    global.__amateur_state.current = current;
  }

  registry.content = '';
  registry.layout_output = '';
  registry.side_title = null;
  registry.config = {};
  registry.container = {};
  registry.target = {};
  if (registry.helpers.sidebar && typeof registry.helpers.sidebar.empty === 'function') {
    registry.helpers.sidebar.empty();
  }
  global.SESSION = current.session;

  if (options.express) {
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
      replaceable('response_content')(result);
    }
    syncSessionCookie(sessionCookie);
    return sendResponse();
  }

  const collectBody = ['POST', 'PUT', 'PATCH', 'DELETE'].includes(req.method);
  if (!collectBody) {
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
      replaceable('response_content')(result);
    }
    syncSessionCookie(sessionCookie);
    return sendResponse();
  }

  let raw = '';
  req.on('data', chunk => {
    raw += chunk;
  });
  req.on('end', () => {
    current.body = raw;
    const contentType = req.headers['content-type'] || '';
    if (contentType.includes('application/x-www-form-urlencoded')) {
      current.params = { ...current.params, ...querystring.parse(raw) };
    }
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
      replaceable('response_content')(result);
    }
    syncSessionCookie(sessionCookie);
    sendResponse();
  });
}

function runOnce(handler, options = {}) {
  const urlValue = options.url || '/';
  const method = options.method || 'GET';
  const headers = options.headers || {};
  const url = new URL(urlValue, `http://${headers.host || 'localhost'}`);
  const query = Object.fromEntries(url.searchParams.entries());
  const cookies = parseCookies(headers.cookie || '');
  const sessionCookie = cookies[SESSION_COOKIE];
  let sessionData = {};
  if (SESSION_SECRET && sessionCookie) {
    if (sessionStore.has(sessionCookie)) {
      sessionData = sessionStore.get(sessionCookie);
    } else {
      const parsed = parseSessionCookie(sessionCookie);
      sessionData = parsed || {};
      sessionStore.set(sessionCookie, sessionData);
    }
  }

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
  if (global.__amateur_state) {
    global.__amateur_state.current = current;
  }
  global.SESSION = current.session;
  global.FILES = {};
  const contentType = headers['content-type'] || '';
  if (contentType.includes('multipart/form-data')) {
    const buffer = Buffer.isBuffer(options.body) ? options.body : Buffer.from(options.body || '', 'latin1');
    const parsed = parseMultipart(buffer, contentType);
    current.params = { ...current.params, ...parsed.fields };
    global.FILES = parsed.files;
  } else if (contentType.includes('application/x-www-form-urlencoded')) {
    current.params = { ...current.params, ...querystring.parse(options.body || '') };
  }

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
    replaceable('response_content')(result);
  }
  syncSessionCookie(sessionCookie);
  return current.response;
}

function initGlobals() {
  global.replaceable = replaceable;
  global._ = value => value;
  global.amateur = module.exports;
  global.__amateur_state = { registry, paths, current: null, handleRequest, generateSessionId };

  applyPhpCompat({
    getCurrent: () => current,
    generateSessionId,
  });

  global.blogmarks = new Proxy(
    {
      registry,
      config: (key, defaultValue, value) => {
        if (!registry.config) {
          registry.config = {};
        }
        if (value !== undefined && value !== null) {
          registry.config[key] = value;
        }
        if (registry.config[key] !== undefined) {
          return registry.config[key];
        }
        return defaultValue;
      },
    },
    {
      get(target, prop) {
        if (prop in target) {
          return target[prop];
        }
        if (prop in global) {
          return global[prop];
        }
        return undefined;
      },
      set(target, prop, value) {
        target[prop] = value;
        return true;
      },
    },
  );

  if (!registry.target) {
    registry.target = {};
  }
  if (!registry.container) {
    registry.container = {};
  }

  loadReplaceables(path.join(__dirname, 'replaceables'));
}

module.exports = {
  amateur: Amateur,
  model: {
    table: BaseTable,
    resource: BaseResource,
    query: BaseQuery,
    db,
  },
  setPaths,
  replaceable,
  callReplaceable,
  loadReplaceables,
  include,
  runOnce,
  initGlobals,
};
