---
title: Site Architecture
category: site
order: 3
excerpt: How this site is put together — Laravel, Livewire single-file components, and a flat-file content store.
---

This site is intentionally boring in the best way: one Laravel app, no database, no CMS, no build pipeline beyond Vite. Everything you're reading is a Markdown file rendered at request time.

## The stack

- **Laravel 13** — routing, views, and the service container.
- **Livewire 4** — every page is a single-file component; search on the doc index is live, no JavaScript written by hand.
- **Tailwind 4** — a custom `ink` palette for the dark terminal look.
- **CommonMark** — Markdown with GitHub-flavored extensions and YAML front matter.

## Request flow

A request to `/docs/{slug}` hits a Livewire route, which mounts a single-file component. The component asks the content repository for the doc and aborts with a 404 if the slug doesn't resolve:

```php
public function mount(string $slug): void
{
    $this->slug = $slug;

    abort_unless(
        app(ContentRepository::class)->doc($slug) !== null,
        404
    );
}
```

Content itself never touches component state — Livewire serializes public properties between requests, so collections stay inside `#[Computed]` methods and only scalars (the slug, a search string) live on the component.

## The content store

`ContentRepository` scans `resources/content/{log,docs}`, parses front matter, renders the Markdown to HTML, and hands back plain arrays. Sorting is done in memory: posts newest-first, docs by `order` then `title`. With a few dozen files this is instant, and the whole "database" fits in a git repo — which means content gets code review, history, and rollbacks for free.

If the site ever outgrows this, the repository is the single seam to swap in a real datastore. So far, it hasn't.
