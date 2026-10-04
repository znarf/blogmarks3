# JS Version Architecture

## Overview

The JS version is a small framework layered under an app port:

- `js-version/amateur/` provides the framework runtime, request handling, and replaceable loader.
- `js-version/application/` holds modules, views, helpers, and layouts translated from PHP.
- `js-version/classes/` holds models, tables, resources, services, and app-level utilities.
- `js-version/replaceables/` is the app-level replaceable set.

## Core Flow

1. `start.js` loads environment and initializes the framework.
2. `Replaceable.load_replaceables()` indexes all replaceables.
3. `Replaceable.expose_replaceables()` exposes helper globals.
4. Requests run through `amateur.handleRequest`, which:
   - builds a per-request `current` context
   - parses params and uploads
   - initializes `SESSION` and `FILES`
   - executes the module handler
   - syncs the session cookie

## Replaceables

Replaceables are the primary extension mechanism.

- `amateur/classes/replaceable.js` is the source of truth.
- `amateur/replaceables/core/` provides thin wrappers for app use.
- App replaceables live in `js-version/replaceables/`.

## Session

Session handling lives in `amateur/classes/session.js`.

- Signed cookies use `SESSION_SECRET`.
- Session data is cached in memory for the process.

## Storage

- SQLite via `better-sqlite3`.
- DB helpers live in `amateur/classes/model/db.js`.
- Cache lives in `amateur/classes/model/cache.js` and is exposed as `global.cache`.

## HTML/Markdown

- `markdown_to_html` uses Showdown.
- `html_to_markdown` uses Turndown.
- Models call these helpers rather than hidden mocks.

## Tests

- Uses `node:test` in `js-version/tests/`.
- `tests/helpers/runtime.js` initializes globals once per test.
