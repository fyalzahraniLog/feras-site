---
title: "Live Search and Tag Filters with Livewire"
date: 2026-07-05
tags: [livewire, tailwind]
excerpt: "Adding grep-style live search and tag filtering to the dev log — computed properties, debounced inputs, and why nothing here touches a database."
---

The log index you're reading this on now has live search and tag filtering, and the whole thing took less code than I expected. No JavaScript was written in the making of this feature — Livewire handles the wire between the input and the server.

## The shape of it

Two public properties hold the UI state, and a computed property derives the filtered list on every render:

```php
public string $search = '';
public string $activeTag = '';

#[Computed]
public function posts()
{
    return app(ContentRepository::class)->posts()
        ->filter(fn ($post) =>
            $this->search === ''
            || stripos($post['title'], $this->search) !== false
            || stripos($post['excerpt'], $this->search) !== false)
        ->filter(fn ($post) =>
            $this->activeTag === ''
            || in_array($this->activeTag, $post['tags']))
        ->values();
}
```

The important rule: **only scalars live in public properties.** Livewire serializes them between requests, and a Collection full of Carbon dates does not survive that trip gracefully. The posts themselves are recomputed from the markdown files each time, which sounds wasteful until you remember there are about five of them.

## Small things that mattered

- `wire:model.live.debounce.300ms` on the search input, so typing "livewire" fires one request instead of eight
- `wire:key` on every card in the loop, so Livewire's DOM diffing doesn't reuse the wrong element when the list reorders
- An explicit empty state — a terminal-style `no results` card with a reset button beats a silently blank page
- Tag buttons toggle: clicking the active tag clears it, which feels more natural than a separate close control

## Styling the terminal look

Tailwind did the heavy lifting: the search box is a mono-font input with a `$` prompt glyph pinned inside it, and the active tag chip flips to `emerald-400` — the same green the section headings use. Consistency over cleverness.

Filtering a handful of markdown files will never be a performance story, but the interaction makes the log feel like a real tool rather than a static page. That was the point.
