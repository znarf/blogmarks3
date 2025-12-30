const express = require('express');
const multer = require('multer');
const os = require('os');

function run(handler, options = {}) {
  const state = global.__amateur_state;
  const port = options.port || process.env.PORT || 3000;
  const host = options.host || process.env.HOST || '127.0.0.1';
  const app = express();
  const upload = multer({ dest: os.tmpdir() });

  app.use(express.urlencoded({ extended: true }));
  app.use(upload.any());
  if (state && state.paths && state.paths.public) {
    app.use(express.static(state.paths.public));
  }
  app.use((req, res) => state.handleRequest(handler, req, res, { express: true }));

  return app.listen(port, host);
}

module.exports = run;
