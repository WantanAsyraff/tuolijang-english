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

## Workflow

1. Add or update the paired entry in the appropriate canonical catalog.
2. Run `npm run i18n:generate` from the client.
3. Run `npm run i18n:check`, `npm run i18n:audit`, and `npm run i18n:test`.
4. Build the affected client.

Generated locale modules are deterministic and committed so each standalone source archive can build without the sibling tooling. Never edit generated modules by hand. `check` compares expected output without rewriting it. The dashboard audit rejects legacy translation methods, dictionaries, engines, locale aliases, and static Chinese display text outside `$()`.