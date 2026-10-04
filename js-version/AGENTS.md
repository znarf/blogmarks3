# JS Version Notes

## What We Learned

- The PHP-style global replaceables work in JS only if they are loaded and exposed early; missing exposure creates hard-to-debug runtime errors.
- Keep framework state per request; reusing registry state across requests in Node leads to sidebar/layout duplication.
- Session persistence is best handled centrally; once cookies are signed, tests must set `SESSION_SECRET` before loading the framework.
- Model resources should read from attributes, not instance properties, because they are decorated from DB rows.
- Markdown ↔ HTML conversion is now explicit utilities; hidden mocks make debugging harder.
- Node test isolation is fragile when using globals; a single runtime initializer keeps tests stable.

## Working Conventions

- Prefer snake_case for framework utilities and replaceables.
- Keep replaceables in the framework, app logic in `js-version/application`.
- Use `Replaceable.expose_replaceables()` to define global helpers; avoid manual globals.
- Run `npm run test:js-version` after refactors that touch framework or replaceables.
