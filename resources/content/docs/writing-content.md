---
title: Writing Content
category: General
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

## Doc pages

Docs live in `resources/content/docs/` and are grouped rather than dated. Front matter drives the index:

- `title` — page heading and link text.
- `category` — the `#`-labeled group on the index and in the sidebar.
- `order` — sort position within all docs (ties broken by title).
- `excerpt` — one line shown on the index card; keep it under ~140 characters.

## Conventions

A few habits that keep the section coherent:

1. One idea per page — if a doc needs a table of contents, split it.
2. Lead with the point; put background at the bottom.
3. Show real commands and real paths, not pseudo-code.
4. Update the file instead of writing "v2" pages — file modification time becomes the `updated` date automatically.

That's the entire pipeline. If you can write Markdown, you can publish here.
