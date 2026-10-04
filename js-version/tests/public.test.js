const assert = require('node:assert');
const test = require('node:test');

const { setupRuntime } = require('./helpers/runtime');

test('public route renders marks page', () => {
  const amateur = setupRuntime();
  const response = amateur.runOnce(() => action('start'), { url: '/marks' });
  assert.strictEqual(response.code, 200);
  assert.ok(response.body.includes('Public Marks'));
});

test('root redirects to /marks', () => {
  const amateur = setupRuntime();
  const response = amateur.runOnce(() => action('start'), { url: '/' });
  assert.strictEqual(response.code, 302);
  assert.strictEqual(response.headers.Location, '/marks');
});
