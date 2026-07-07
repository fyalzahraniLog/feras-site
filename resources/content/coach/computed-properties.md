---
title: "Computed Properties"
updated: "2026-07-07"
---

You're going to build the state handling of the log post page the way every ⚡ component on this site does it: a scalar slug in a public property, and everything the repository returns derived in `#[Computed]` methods. Along the way you'll see exactly why a Collection full of Carbon dates must never touch a public property.

## Store the route param as a scalar

```php
public string $slug = '';

public function mount(string $slug): void
{
    $this->slug = $slug;

    if (app(\App\Content\ContentRepository::class)->post($slug) === null) {
        abort(404);
    }
}
```

This is the top of `resources/views/components/⚡log-show.blade.php`. Between requests, Livewire serializes every public property to JSON, ships it to the browser, and hydrates it back on the next interaction — so whatever you put here makes that round trip every time. A plain string survives it perfectly, which is why the component keeps only the slug and uses `mount()` to 404 early if it matches nothing.

## Trace what the repository returns

```php
public function posts(): Collection
{
    return $this->parseDirectory($this->contentPath.'/log')
        ->map(fn (array $entry) => $this->hydratePost($entry))
        ->sortByDesc('date')
        ->values();
}
```

This is `app/Content/ContentRepository.php` — and it's the data you might be tempted to store next: a Collection where every post carries a Carbon instance under `date` and the full rendered HTML under `html`. If you assigned that to a public property, Livewire would have to dehydrate all of it into the JSON snapshot on every interaction, send it to the client, and rebuild it on the way back. That makes snapshots large, hydration fragile, and the data stale — the client keeps echoing back whatever was serialized on first load. So don't store it; derive it.

## Derive the post with #[Computed]

```php
use Livewire\Attributes\Computed;

#[Computed]
public function post()
{
    return app(\App\Content\ContentRepository::class)->post($this->slug);
}

public function rendering($view): void
{
    $view->title($this->post['title'].' — Log Dev — Feras');
}
```

Mark a method with `#[Computed]` and it behaves like a dynamic property: nothing is stored on the component, and each render pulls fresh data from the repository using the scalar `$slug` you kept in step 1. Livewire also caches the value for the duration of the request — `rendering()` reads `$this->post` here, the template will read it several more times, and the markdown file is still parsed only once. You get the ergonomics of a property with the cost of a single method call, and the result never becomes part of the component's payload.

## Read it in the template with $this->

```blade
<h1>{{ $this->post['title'] }}</h1>

<time datetime="{{ $this->post['date']->format('Y-m-d') }}">
    {{ $this->post['date']->format('Y-m-d') }}
</time>

<x-prose :html="$this->post['html']" />
```

In Blade you access a computed property through `$this`, as if it were a plain property — no parentheses. The prefix is what distinguishes it from a public property: something like `$search` would be available directly, while `$this->post` routes through the computed method (and hits the per-request cache on every access after the first). That's the whole convention on this site: route params and filter state as scalar public properties, everything the repository returns behind `#[Computed]`. Follow the same shape when you add a new page.
