function run(handler, options = {}) {
  const state = global.__amateur_state;
  const port = options.port || process.env.PORT || 3000;
  const host = options.host || process.env.HOST || '127.0.0.1';
  const express = require('express');
  const app = express();

  app.use(express.urlencoded({ extended: true }));
  app.use(express.raw({ type: 'multipart/form-data', limit: '20mb' }));
  if (state && state.paths && state.paths.public) {
    app.use(express.static(state.paths.public));
  }
  app.use((req, res) => state.handleRequest(handler, req, res, { express: true }));

  return app.listen(port, host);
}

module.exports = run;
