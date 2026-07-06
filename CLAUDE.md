# feras-site — personal CV / portfolio site

Public repo: employers will read this code. The full quality bar applies (see bottom).

## Stack & architecture

- Laravel 13 + Livewire 4 + Tailwind 4. Livewire pages are single-file components at `resources/views/components/⚡*.blade.php` (the filename really starts with ⚡).
- **No database.** Content is Markdown + YAML front matter in `resources/content/{log,docs}`, parsed by `app/Content/ContentRepository.php` (memoized singleton). Sessions use the cookie driver, cache uses file. Keep it DB-free — the deploy target is Vercel.
- Routing: `Route::livewire()` only, in `routes/web.php`. Shared layout: `resources/views/layouts/app.blade.php`.
- Cursor black-hole effect: `resources/js/blackhole.js` (site-wide canvas grid; static CSS grid is the reduced-motion/touch fallback).

## Conventions

- Design system: dark terminal theme — custom `ink` palette (tokens in `resources/css/app.css`), emerald-400 primary accent, cyan-300 links/tags, `font-mono` for headings, dates (`Y-m-d`), and labels. Shared Blade components: `x-section-heading`, `x-tag`, `x-prose`, `x-branch-badge` — reuse before inventing.
- Livewire rule: public properties hold **scalars only**; derive Collections/Carbon data in `#[Computed]` methods (public props are serialized between requests).
- Front matter: always **quote dates** (`date: "2026-07-06T14:00:00+03:00"`) — unquoted YAML dates are coerced to integer timestamps.
- Doc pages: no h1 in the body (title comes from front matter); `##`/`###` feed the "On this page" TOC.

## Quality bar — public portfolio, no exceptions

1. Every meaningful change gets a Pest test (content parsing, route responses, filter behavior).
2. Run `vendor/bin/pint --dirty` before committing.
3. `npm run build` must pass before committing asset changes.
4. Update `attacksurface.md` whenever deployment or exposure changes.

## Agents that write content here

- `/log-dev` registers git-branch activity into `resources/content/log/`. **Private projects (PcHome) are never registered without explicit approval.**
- `/doc-dev` writes/updates tutorial pages in `resources/content/docs/` — it must fetch the official Laravel/Livewire docs before writing.
