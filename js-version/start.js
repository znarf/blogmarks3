const dotenv = require('dotenv');
const path = require('path');

dotenv.config({ path: path.join(__dirname, '.env') });

const amateur = require('./amateur/amateur');

// Init Amateur
amateur.initGlobals();

// Load application replaceables
amateur.Replaceable.load_replaceables(path.join(__dirname, 'replaceables'));
// Expose all replaceables
amateur.Replaceable.expose_replaceables();

const Blogmarks = require('./classes/blogmarks');
global.blogmarks = new Blogmarks();

// Configure application directory
app_dir(path.join(__dirname, 'application'));

if (process.env.DRY_RUN === '1') {
  const response = amateur.runOnce(() => action('start'), {
    url: process.env.DRY_RUN_URL || '/',
  });
  const output = {
    status: response.code,
    headers: response.headers,
    bodyLength: response.body.length,
  };
  if (process.env.DRY_RUN_BODY === '1') {
    output.body = response.body;
  }
  console.log(JSON.stringify(output, null, 2));
} else {
  const port = process.env.PORT ? Number(process.env.PORT) : 8002;
  const host = process.env.HOST || '127.0.0.1';
  run(() => action('start'), { port, host });
}
