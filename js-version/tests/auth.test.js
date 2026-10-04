const assert = require('node:assert');
const test = require('node:test');
const path = require('path');
const Database = require('better-sqlite3');

const { setupRuntime } = require('./helpers/runtime');

test('auth signin renders form with token', () => {
  const amateur = setupRuntime();
  const response = amateur.runOnce(() => action('start'), { url: '/auth/signin' });
  assert.strictEqual(response.code, 200);
  assert.ok(response.body.includes('name="token"'));
  assert.ok(response.body.includes('action="/auth/signin"'));
});

test('auth sign in persists session and loads my marks', () => {
  const amateur = setupRuntime();
  const db = new Database(path.join(__dirname, '..', '..', 'blogmarks.sqlite'));
  const now = new Date().toISOString().slice(0, 19).replace('T', ' ');
  const unique = `${Date.now()}_${Math.random().toString(16).slice(2)}`;
  const login = `test_user_${unique}`;
  const email = `${login}@example.com`;
  const linkHref = `https://example.com/test/${login}`;
  const password = 'secret-pass';
  let userId;
  let linkId;
  let markId;

  try {
    db.prepare('DELETE FROM bm_users WHERE login = ? OR email = ?').run(login, email);
    const userInsert = db
      .prepare(
        `INSERT INTO bm_users (name, email, url, login, pass, permlevel, avatar, timezone, lang, ip, updated, code, activationkey)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      )
      .run('Test User', email, '', login, password_hash(password), 0, '', '1', 0, '127.0.0.1', now, '', '');
    userId = userInsert.lastInsertRowid;

    db.prepare('DELETE FROM bm_links WHERE href = ?').run(linkHref);
    const linkInsert = db.prepare('INSERT INTO bm_links (href) VALUES (?)').run(linkHref);
    linkId = linkInsert.lastInsertRowid;

    const markInsert = db
      .prepare(
        `INSERT INTO bm_marks (title, published, updated, related, author, content, contentType, visibility, display)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      )
      .run('Test Private Mark', now, now, linkId, userId, 'Test content', 'text', 1, 1);
    markId = markInsert.lastInsertRowid;

    const signinPage = amateur.runOnce(() => action('start'), {
      url: '/auth/signin',
    });
    assert.strictEqual(signinPage.code, 200);
    const cookie = signinPage.headers['Set-Cookie'];
    assert.ok(cookie, 'expected session cookie from signin page');
    const tokenMatch = signinPage.body.match(/name="token" value="([^"]+)"/);
    assert.ok(tokenMatch, 'expected csrf token in signin form');
    const token = tokenMatch[1];

    const signinPost = amateur.runOnce(() => action('start'), {
      url: '/auth/signin',
      method: 'POST',
      headers: { cookie },
      params: {
        username: login,
        password,
        token,
        redirect_url: '/my/marks',
      },
    });
    assert.strictEqual(signinPost.code, 302);
    assert.strictEqual(signinPost.headers.Location, '/my/marks');

    const myMarks = amateur.runOnce(() => action('start'), {
      url: '/my/marks',
      headers: { cookie },
    });
    assert.strictEqual(myMarks.code, 200);
    assert.ok(myMarks.body.includes('Test Private Mark'));
  } finally {
    if (markId) {
      db.prepare('DELETE FROM bm_marks WHERE id = ?').run(markId);
    }
    if (linkId) {
      db.prepare('DELETE FROM bm_links WHERE id = ?').run(linkId);
    }
    if (userId) {
      db.prepare('DELETE FROM bm_users WHERE id = ?').run(userId);
    }
  }
});
