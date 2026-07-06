---
title: "Computed Properties"
category: livewire
order: 2
excerpt: "Why this site derives all Collections and dates in #[Computed] methods instead of storing them on public properties."
updated: "2026-07-06"
---

Livewire components hold state in public properties, but not all state should live there. Between requests, Livewire serializes every public property to JSON, ships it to the browser, and hydrates it back on the next interaction. That round trip is cheap for a search string; it is wasteful — and lossy — for a Collection of parsed markdown posts with Carbon dates inside. Computed properties solve this: they are derived on the server, on demand, and never serialized.

## The #[Computed] attribute

Mark any component method with `#[Computed]` and it behaves like a dynamic property. On the homepage, `resources/views/components/⚡home.blade.php` derives its two content lists this way:

```php
#[Computed]
public function posts()
{
    return app(\App\Content\ContentRepository::class)->posts()->take(3);
}

#[Computed]
public function docs()
{
    return app(\App\Content\ContentRepository::class)->docs()->take(4);
}
```

Nothing is stored on the component. Each render, the method pulls fresh data from the content repository, and the result never becomes part of the component's payload.

## Accessing computed properties in the template

In the template you access a computed property through `$this`, as if it were a plain property — no parentheses. The homepage loops over its posts like this:

```blade
@forelse ($this->posts as $post)
    <a href="{{ route('log.show', $post['slug']) }}"
       wire:key="post-{{ $post['slug'] }}"
       ...>
```

The `$this->` prefix is what distinguishes computed properties from public ones in Blade: a public property like `$search` is available directly, while `$this->posts` routes through the computed method.

## Per-request caching

Livewire caches a computed property's value for the duration of the request. The first access runs the method; every later access in the same request returns the memoized result. You can see why that matters in `resources/views/components/⚡docs-show.blade.php`, where `doc` is read in `rendering()`, in the header, and in the breadcrumb:

```php
#[Computed]
public function doc(): array
{
    return app(\App\Content\ContentRepository::class)->doc($this->slug);
}

public function rendering($view): void
{
    $view->title($this->doc['title'].' — DOC — Feras');
}
```

The repository lookup — reading and parsing a markdown file — happens once per request no matter how many times the template touches `$this->doc`. You get the ergonomics of a property with the cost of a single method call.

## The site's rule: scalars in, everything else derived

This site follows one strict convention: **public properties hold only scalars; Collections and Carbon data are always derived in computed methods.** Look at any ⚡ component and you'll see it. `⚡log-show.blade.php` stores just the slug from the route:

```php
public string $slug = '';

public function mount(string $slug): void
{
    $this->slug = $slug;
    // ...
}
```

The post itself — an array containing a Carbon date, rendered HTML, and tags — comes from the `post()` computed property, and the previous/next links come from a second computed, `adjacent()`, which searches the full posts Collection by slug.

The reason is the serialization round trip described above. If `$post` were public, Livewire would have to dehydrate the Carbon instance and the parsed HTML into the JSON snapshot on every interaction, send it to the client, and rebuild it on the way back. Complex objects make snapshots large, hydration fragile, and stale data possible — the client would keep echoing back whatever was serialized on first load. A scalar `$slug` survives the trip perfectly, and the expensive data is re-derived server-side where it is always fresh and never leaves.

When you add a new page to this site, follow the same shape: route params and filter state as scalar public properties, everything the repository returns behind `#[Computed]`.

## Further reading

- [Computed Properties](https://livewire.laravel.com/docs/computed-properties) — the attribute, caching, and when to use them
- [Properties](https://livewire.laravel.com/docs/properties) — how public properties are serialized between requests
