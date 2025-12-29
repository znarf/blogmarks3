const nodeCrypto = require('node:crypto');

const sessionStore = new Map();

function session_secret() {
  return process.env.SESSION_SECRET || '';
}

function session_cookie() {
  return process.env.SESSION_COOKIE || 'bm_session';
}

function sign_session_payload(payload) {
  const secret = session_secret();
  if (!secret) {
    return '';
  }
  return nodeCrypto.createHmac('sha256', secret).update(payload).digest('hex');
}

function parse_session_cookie(value) {
  const secret = session_secret();
  if (!secret || !value) {
    return null;
  }
  const [payload, sig] = value.split('.');
  if (!payload || !sig) {
    return null;
  }
  const expected = sign_session_payload(payload);
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

function serialize_session_cookie(data) {
  if (!session_secret()) {
    return null;
  }
  const json = JSON.stringify(data || {});
  const payload = Buffer.from(json, 'utf8').toString('base64');
  const sig = sign_session_payload(payload);
  if (!sig) {
    return null;
  }
  return `${payload}.${sig}`;
}

function parse_cookies(header = '') {
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

function resolve_session_data(sessionCookie) {
  if (!session_secret() || !sessionCookie) {
    return {};
  }
  if (sessionStore.has(sessionCookie)) {
    return sessionStore.get(sessionCookie);
  }
  const parsed = parse_session_cookie(sessionCookie);
  const sessionData = parsed || {};
  sessionStore.set(sessionCookie, sessionData);
  return sessionData;
}

function sync_session_cookie(current, sessionCookie) {
  if (!session_secret()) {
    return;
  }
  if (!sessionCookie && current && current.session_id) {
    sessionCookie = current.session_id;
  }
  const payload = serialize_session_cookie(current.session);
  if (!payload) {
    return;
  }
  const cookie = `${session_cookie()}=${encodeURIComponent(payload)}; Path=/; HttpOnly`;
  if (current.response.headers['Set-Cookie'] !== cookie) {
    current.response.headers['Set-Cookie'] = cookie;
  }
}

module.exports = {
  session_cookie,
  parse_cookies,
  resolve_session_data,
  sync_session_cookie,
};
