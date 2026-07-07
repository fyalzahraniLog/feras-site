---
title: Writing Content
category: site
order: 2
excerpt: The workflow for adding dev-log posts and doc pages — front matter, file naming, and conventions.
---

All content on this site is Markdown with YAML front matter. There is no admin panel: writing a post means opening an editor, saving a file, and pushing.

## Dev-log posts

Posts live in `resources/content/log/` and follow a dated filename convention — the date prefix keeps the directory sorted and the slug is derived from the rest:

```markdown
---
title: Shipping the search box
date: 2026-07-04
tags: [livewire, tailwind]
excerpt: A live-filtered doc index in ~30 lines of component code.
---

Body starts here. GitHub-flavored Markdown, so tables,
task lists and fenced code blocks all work.
```

Save that as `2026-07-04-shipping-the-search-box.md` and it appears at `/log/shipping-the-search-box`. Reading time is computed from the word count, so there's nothing to maintain.

## Activity entries (Log Dev)

The Log Dev feed also mixes in **activity entries** — GitHub-style contribution cards registered by the `/log-dev` agent after working on a project branch. The card shows the project and branch; the bullet details expand inline. An activity file adds a few front-matter fields:

```markdown
---
type: activity
title: "One-line summary of the branch work"
date: "2026-07-06T14:32:10+03:00"
project: PcHome
branch: feature/checkout
repo: https://github.com/user/repo
commit: 4f9c2e1
slug: 2026-07-06-pchome-feature-checkout
tags: [pchome]
---

- Bullet points summarizing what was accomplished.
```

Three rules the agent follows (and you should too, if writing one by hand): quote the `date` (unquoted YAML datetimes become integer timestamps), keep the date prefix in the explicit `slug` (prevents collisions when the same branch is registered on different days), and record the HEAD `commit` — it's how the agent knows where the next summary should start.

## Doc pages

Docs live in `resources/content/docs/` and are grouped rather than dated. Front matter drives the index:

- `title` — page heading and link text (quote it).
- `category` — the `#`-labeled group on the index and in the sidebar. Current groups, in fixed sidebar order: `laravel`, `livewire`, `site` (the order lives in `ContentRepository::CATEGORY_ORDER`; unknown categories sort last).
- `order` — sort position within the category (ties broken by title).
- `excerpt` — one line shown on the index card; keep it under ~140 characters (quote it).
- `updated` — quoted `"YYYY-MM-DD"`. Quote every date in front matter: unquoted YAML dates are coerced to integer timestamps by the parser.

Body rules for doc pages:

- **No h1** — the page title renders from front matter.
- `##` and `###` headings get anchor ids and become the page's "On this page" table of contents, so every `##` should be a meaningful section.

## Coach walkthroughs

Doc pages in the `laravel` and `livewire` categories can have a **feras-coach walkthrough** — a guided, code-first, step-by-step version of the page, rendered at `/docs/{slug}/coach`. Walkthroughs live in `resources/content/coach/` and the **filename must equal the doc slug it coaches** (`coach/routing.md` coaches `/docs/routing`).

```markdown
---
title: "Routing with Route::livewire"
updated: "2026-07-07"
---

Optional intro (shown with step 1): two sentences on what will exist by the end.

## Register the route

```php
Route::livewire('/docs/{slug}', 'docs-show')->name('docs.show');
```

Explanation of what the reader just saw...
```

Rules the parser and the coach page depend on:

- Every `##` heading is **one step**; text before the first `##` is the intro. No h1.
- **The first element of every step body is a fenced code block** — the coach page shows the code above its explanation by design.
- Quote `title` and `updated` in front matter (unquoted YAML dates become integer timestamps).
- Only `laravel`/`livewire` docs get walkthroughs, and only for doc pages that exist.
- Reading progress is stored in the reader's own browser (localStorage) — nothing server-side.

## Conventions

A few habits that keep the section coherent:

1. One idea per page — if a doc needs a table of contents, split it.
2. Lead with the point; put background at the bottom.
3. Show real commands and real paths, not pseudo-code.
4. Update the file instead of writing "v2" pages — file modification time becomes the `updated` date automatically.

That's the entire pipeline. If you can write Markdown, you can publish here.
