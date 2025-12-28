const dotenv = require('dotenv');
const path = require('path');

dotenv.config({ path: path.join(__dirname, '.env') });

const amateur = require('./amateur/amateur');

const root = __dirname;

amateur.setPaths({
  root,
  replaceables: path.join(root, 'replaceables'),
  views: path.join(root, 'application/views'),
  layouts: path.join(root, 'application/layouts'),
  partials: path.join(root, 'application/partials'),
  renders: path.join(root, 'application/renders'),
  modules: path.join(root, 'application/modules'),
  helpers: path.join(root, 'application/helpers'),
  actions: path.join(root, 'application'),
  public: path.join(root, '..', 'public'),
});

amateur.initGlobals();
amateur.loadReplaceables(path.join(root, 'replaceables'));
global.registry = require('./classes/registry');
amateur.replaceable('expose_replaceables')();

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
