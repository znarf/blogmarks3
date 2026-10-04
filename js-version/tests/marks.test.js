const assert = require('node:assert');
const test = require('node:test');
const path = require('path');
const Database = require('better-sqlite3');

const { setupRuntime } = require('./helpers/runtime');
const Mark = require('../classes/model/resource/mark');

test('edit mark modal shows existing content and updates mark', () => {
  const amateur = setupRuntime();
  const db = new Database(path.join(__dirname, '..', '..', 'blogmarks.sqlite'));
  const now = new Date().toISOString().slice(0, 19).replace('T', ' ');
  const unique = `${Date.now()}_${Math.random().toString(16).slice(2)}`;
  const login = `test_user_${unique}`;
  const email = `${login}@example.com`;
  const password = 'secret-pass';
  let userId;
  let linkId;
  let markId;

  try {
    const userInsert = db
      .prepare(
        `INSERT INTO bm_users (name, email, url, login, pass, permlevel, avatar, timezone, lang, ip, updated, code, activationkey)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      )
      .run('Test User', email, '', login, password_hash(password), 0, '', '1', 0, '127.0.0.1', now, '', '');
    userId = userInsert.lastInsertRowid;

    const linkInsert = db.prepare('INSERT INTO bm_links (href) VALUES (?)').run('https://example.com/edit');
    linkId = linkInsert.lastInsertRowid;

    const markInsert = db
      .prepare(
        `INSERT INTO bm_marks (title, published, updated, related, author, content, contentType, visibility, display)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      )
      .run('Editable Mark', now, now, linkId, userId, 'Editable content', 'text', 1, 1);
    markId = markInsert.lastInsertRowid;

    const signinPage = amateur.runOnce(() => action('start'), {
      url: '/auth/signin',
    });
    const cookie = signinPage.headers['Set-Cookie'];
    const tokenMatch = signinPage.body.match(/name="token" value="([^"]+)"/);
    const token = tokenMatch ? tokenMatch[1] : null;

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

    const editModal = amateur.runOnce(() => action('start'), {
      url: `/my/marks/${markId},edit`,
      headers: { cookie },
      params: { modal: 1 },
    });
    assert.strictEqual(editModal.code, 200);
    assert.ok(editModal.body.includes('Editable Mark'));
    assert.ok(editModal.body.includes('Editable content'));

    const editTokenMatch = editModal.body.match(/name="token" value="([^"]+)"/);
    const editToken = editTokenMatch ? editTokenMatch[1] : null;
    assert.ok(editToken, 'expected csrf token in edit form');

    const updatePost = amateur.runOnce(() => action('start'), {
      url: `/my/marks/${markId},edit`,
      method: 'POST',
      headers: { cookie },
      params: {
        save: '1',
        token: editToken,
        url: 'https://example.com/edit',
        title: 'Updated Mark',
        description: 'Updated content',
        visibility: 1,
        tags: 'foo, bar',
        private_tags: 'secret',
        referer: '/my/marks',
      },
    });
    assert.strictEqual(updatePost.code, 302);
    assert.strictEqual(updatePost.headers.Location, `/my/marks#mark${markId}`);

    const updatedRow = db.prepare('SELECT title, content FROM bm_marks WHERE id = ?').get(markId);
    assert.strictEqual(updatedRow.title, 'Updated Mark');
    assert.strictEqual(updatedRow.content, '<p>Updated content</p>');
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

test('html marks are converted to markdown in text()', () => {
  setupRuntime();
  const mark = new Mark({
    contentType: 'html',
    content: '<p>Hello <strong>world</strong></p>',
  });
  const output = mark.text();
  assert.ok(output.includes('Hello'));
  assert.ok(output.includes('**world**'));
});

test('markdown is converted to html with markdown_to_html()', () => {
  setupRuntime();
  const html = markdown_to_html('**bold**');
  assert.ok(html.includes('<strong>bold</strong>'));
});
