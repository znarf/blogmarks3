const assert = require('node:assert');
const test = require('node:test');
const fs = require('fs');
const path = require('path');
const Database = require('better-sqlite3');

const { setupRuntime } = require('./helpers/runtime');

test('tools import uploads a file and inserts marks', () => {
  const amateur = setupRuntime();
  const db = new Database(path.join(__dirname, '..', '..', 'blogmarks.sqlite'));
  const now = new Date().toISOString().slice(0, 19).replace('T', ' ');
  const unique = `${Date.now()}_${Math.random().toString(16).slice(2)}`;
  const login = `test_user_${unique}`;
  const email = `${login}@example.com`;
  const href = `https://example.com/import/${login}`;
  const password = 'secret-pass';
  let userId;
  let linkId;
  let markId;
  let tagId;
  let tmpFile;

  try {
    const userInsert = db
      .prepare(
        `INSERT INTO bm_users (name, email, url, login, pass, permlevel, avatar, timezone, lang, ip, updated, code, activationkey)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`
      )
      .run(
        'Test User',
        email,
        '',
        login,
        password_hash(password),
        0,
        '',
        '1',
        0,
        '127.0.0.1',
        now,
        '',
        ''
      );
    userId = userInsert.lastInsertRowid;

    const signinPage = amateur.runOnce(() => action('start'), { url: '/auth/signin' });
    const cookie = signinPage.headers['Set-Cookie'];
    const tokenMatch = signinPage.body.match(/name="token" value="([^"]+)"/);
    const token = tokenMatch ? tokenMatch[1] : null;
    assert.ok(cookie, 'expected session cookie from signin page');
    assert.ok(token, 'expected csrf token in signin form');

    const signinPost = amateur.runOnce(() => action('start'), {
      url: '/auth/signin',
      method: 'POST',
      headers: { cookie },
      params: {
        username: login,
        password,
        token,
        redirect_url: '/my/tools,import'
      }
    });
    assert.strictEqual(signinPost.code, 302);

    const importPage = amateur.runOnce(() => action('start'), {
      url: '/my/tools,import',
      headers: { cookie }
    });
    const importTokenMatch = importPage.body.match(/name="token" value="([^"]+)"/);
    const importToken = importTokenMatch ? importTokenMatch[1] : null;
    assert.ok(importToken, 'expected csrf token in import form');

    const atom = `<?xml version="1.0"?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:bm="http://blogmarks.net/ns/">
  <entry>
    <title>Imported Mark</title>
    <updated>2020-01-01T00:00:00Z</updated>
    <published>2020-01-01T00:00:00Z</published>
    <link rel="related" href="${href}" />
    <category scheme="http://blogmarks.net/tag/" label="imported" />
    <content type="text">Imported content</content>
  </entry>
</feed>`;

    tmpFile = path.join('/tmp', `import_${unique}.xml`);
    fs.writeFileSync(tmpFile, atom);

    const importPost = amateur.runOnce(() => action('start'), {
      url: '/my/tools,import',
      method: 'POST',
      headers: { cookie },
      params: { token: importToken },
      files: {
        file: {
          name: 'import.xml',
          type: 'application/xml',
          tmp_name: tmpFile,
          error: 0,
          size: atom.length
        }
      }
    });
    assert.strictEqual(importPost.code, 200);

    const linkRow = db.prepare('SELECT id FROM bm_links WHERE href = ?').get(href);
    linkId = linkRow ? linkRow.id : null;
    const markRow = db
      .prepare('SELECT id, title FROM bm_marks WHERE related = ? AND author = ?')
      .get(linkId, userId);
    markId = markRow ? markRow.id : null;
    assert.ok(markId, 'expected imported mark');

    const tagRow = db.prepare('SELECT id FROM bm_tags WHERE label = ?').get('imported');
    tagId = tagRow ? tagRow.id : null;
  } finally {
    if (markId) {
      db.prepare('DELETE FROM bm_marks_has_bm_tags WHERE mark_id = ?').run(markId);
      db.prepare('DELETE FROM bm_marks WHERE id = ?').run(markId);
    }
    if (tagId) {
      db.prepare('DELETE FROM bm_tags WHERE id = ?').run(tagId);
    }
    if (linkId) {
      db.prepare('DELETE FROM bm_links WHERE id = ?').run(linkId);
    }
    if (userId) {
      db.prepare('DELETE FROM bm_users WHERE id = ?').run(userId);
    }
    if (tmpFile) {
      try {
        fs.unlinkSync(tmpFile);
      } catch (error) {
        // ignore cleanup errors
      }
    }
  }
});
