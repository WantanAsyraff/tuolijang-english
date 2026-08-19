# Shared localization

This directory is the only editable source for frontend English and Simplified Chinese translations.

## Dashboard contract

The management dashboard has one application translation interface:

```js
$('login.title')
$('comments.count', { count: 3 })
$('保存')
$('保存成功', 'Saved successfully')
```

`$()` first resolves a canonical key. In Chinese mode, non-key source text is returned unchanged. In English mode, backend-provided English wins, followed by the exact canonical runtime index and the small allowlist of parameterized count/date formats. Unknown values remain unchanged. Only pass dynamic content when it is known to be system-owned; never translate user-entered or business-record text implicitly.

The dashboard adapter owns the reactive `zh-cn`/`en` state, persists the `language` key, synchronizes Element UI and Moment, and preserves the `laravel_lang` request header. Dependency-owned locale packages remain internal implementation details.

## Catalogs

- `catalogs/common.json` contains system-owned runtime text shared by clients.
- `catalogs/web.json`, `chat.json`, and `mobile.json` contain keyed UI owned by each client.
- Every entry has adjacent `zh-cn` and `en` strings.
- Use `runtime: true` only for backend/system text that may arrive without a stable frontend key.
- Keys must be unique across all catalogs; placeholders must match between locale values.

## SQL-backed dashboard text

`i18n:audit --app web` scans every tracked first-party SQL file below `database` and `public/install` without modifying SQL. `sql-audit-policy.json` classifies each Chinese occurrence as `USER_VISIBLE`, `INTERNAL_VALUE`, `IDENTIFIER`, `COMMENT`, `DATABASE_METADATA`, `TEST_DATA`, or `UNKNOWN`. The audit fails when a value remains `UNKNOWN`, a `USER_VISIBLE` value has no non-Chinese runtime result, a declared frontend boundary stops using `$()`, or the generated locale is stale.

When adding SQL seed data:

1. Identify the table and column in `sql-audit-policy.json`. Keep IDs, parent IDs, area codes, routes, API paths, field identifiers, enum/dictionary values used by logic, JSON keys, and comparison operands protected.
2. Classify only confirmed system-owned labels as `USER_VISIBLE`. Customer names, notes, addresses, announcement bodies, authored legal text, AI prompt bodies, and other business content must remain unchanged. Default agreement and Chat application titles are localized; their editable bodies stay protected until an approved English body is supplied by the owning team.
3. Add one deduplicated `runtime: true` pair under the `sql.*` namespace in `catalogs/common.json`. Reuse existing wording whenever an exact canonical value already exists.
4. Run generation, check, audit, tests, and the dashboard build. Only `i18n:generate` may update generated locale modules.

Administrative-area rows retain their raw SQL names, IDs, parent relationships, and codes. Existing canonical English names and curated municipality/autonomous-region/polyphonic overrides take precedence; deterministic `pinyin-pro` transliteration with an administrative suffix is used only when no established display name exists. `pinyin-pro` remains a dashboard dependency during this phase.

The audit prints deterministic totals by classification, SQL file, table, column, mapping status, and declared frontend-boundary coverage. Any newly discovered table/column must be explicitly classified; do not silence it with a broad catch-all rule.
## Workflow

1. Add or update the paired entry in the appropriate canonical catalog.
2. Run `npm run i18n:generate` from the client.
3. Run `npm run i18n:check`, `npm run i18n:audit`, and `npm run i18n:test`.
4. Build the affected client.

Generated locale modules are deterministic and committed so each standalone source archive can build without the sibling tooling. Never edit generated modules by hand. `check` compares expected output without rewriting it. The dashboard audit rejects legacy translation methods, dictionaries, engines, locale aliases, and static Chinese display text outside `$()`.