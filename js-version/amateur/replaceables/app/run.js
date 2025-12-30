const express = require('express');
const multer = require('multer');
const os = require('os');
const path = require('path');

function run(handler, options = {}) {
  const port = options.port || process.env.PORT || 3000;
  const host = options.host || process.env.HOST || '127.0.0.1';
  const app = express();
  const upload = multer({ dest: os.tmpdir() });

  app.use(express.urlencoded({ extended: true }));
  app.use(upload.any());
  const base = app_dir();
  if (base) {
    const publicDir = path.join(base, '..', '..', 'public');
    app.use(express.static(publicDir));
  }
  app.use((req, res) => amateur.handleRequest(handler, req, res, { express: true }));

  return app.listen(port, host);
}

module.exports = run;
