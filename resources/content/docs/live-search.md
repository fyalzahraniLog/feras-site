---
title: "Live Search & Filtering"
category: livewire
order: 3
excerpt: "How the log and docs pages filter content as you type, using wire:model.live, actions, and computed filtering."
updated: "2026-07-06"
---

The log page (`/log`) filters posts three ways at once — a search box, tag chips, and time-window chips — with no page reload and no hand-written JavaScript. The docs index does the same with a single search box. The pattern is always the same: bind the filter state to public properties, mutate that state through bindings and actions, and do the actual filtering inside a computed property that re-runs on every render.

## Binding the search box with wire:model.live

By default, `wire:model` only syncs on events like `change`. For search-as-you-type you want live updates — but not one network request per keystroke. The search input in `resources/views/components/⚡log-index.blade.php` combines the `live` modifier with a debounce:

```blade
<input
    type="text"
    wire:model.live.debounce.300ms="search"
    placeholder="grep posts..."
    aria-label="Search posts"
    class="w-full bg-transparent font-mono text-sm text-ink-100 placeholder-ink-500 focus:outline-none"
/>
```

`live` sends updates to the server as you type; `debounce.300ms` waits until you pause for 300 milliseconds before sending, so a quickly typed word costs one request instead of eight. `⚡docs-index.blade.php` uses the identical binding for its docs search.

## Toggling filters with actions

The chips are buttons wired to component methods via `wire:click`. Clicking a tag calls `toggleTag()`, and clicking a time window calls `setWindow()` — both defined in `⚡log-index.blade.php`:

```php
public function toggleTag(string $tag): void
{
    $this->activeTag = $this->activeTag === $tag ? '' : $tag;
}

public function setWindow(string $window): void
{
    $this->window = $this->window === $window ? '' : $window;
}
```

Each action toggles: clicking the active chip clears it, clicking another switches to it. In the template the argument is passed inline, and the chip styles itself against the current state:

```blade
<button
    type="button"
    wire:key="tag-{{ $tag }}"
    wire:click="toggleTag('{{ $tag }}')"
    @class([
        'inline-flex items-center rounded border px-2 py-0.5 font-mono text-xs transition-colors',
        'border-emerald-400/40 bg-emerald-400/10 text-emerald-300' => $activeTag === $tag,
        'border-ink-700/80 bg-ink-900 text-cyan-300 hover:border-ink-600' => $activeTag !== $tag,
    ])
>{{ $tag }}</button>
```

For trivial assignments you can skip the method entirely: the "all" chip uses Livewire's magic `$set` action, `wire:click="$set('activeTag', '')"`.

## Filtering inside a computed property

The filters themselves never touch the post list — they only change `$search`, `$activeTag`, and `$window`. The list is derived in the `posts` computed property, which re-evaluates on every render, so any state change automatically produces a freshly filtered Collection:

```php
#[Computed]
public function posts()
{
    $cutoff = match ($this->window) {
        'day' => now()->subDay(),
        'week' => now()->subWeek(),
        'month' => now()->subMonth(),
        default => null,
    };

    return app(\App\Content\ContentRepository::class)
        ->posts()
        ->filter(function (array $post) use ($cutoff) {
            if ($this->search !== ''
                && stripos($post['title'], $this->search) === false
                && stripos($post['excerpt'], $this->search) === false
                // ...
            ) {
                return false;
            }

            if ($this->activeTag !== '' && ! in_array($this->activeTag, $post['tags'], true)) {
                return false;
            }

            if ($cutoff !== null && $post['date']->lt($cutoff)) {
                return false;
            }

            return true;
        })
        ->values();
}
```

All three filters compose in one pass, and the state that drives them stays scalar — the convention this site follows everywhere (see [Computed Properties](/docs/computed-properties)).

## wire:key in loops

When a filtered list changes, Livewire diffs the new HTML against the old. Give every loop item a stable, unique `wire:key` so elements are tracked by identity rather than position:

```blade
@foreach ($this->posts as $post)
    <li wire:key="post-{{ $post['slug'] }}">
```

Without keys, filtering can cause Livewire to reuse the wrong DOM nodes — open `<details>` elements staying open on the wrong post, for example. Every loop in the ⚡ components carries a key, including the chip loops (`wire:key="tag-{{ $tag }}"`).

## The empty state and reset

When every post is filtered out, the page shows what failed and offers a one-click way back. The reset button is just another action:

```blade
<button
    type="button"
    wire:click="resetFilters"
    class="mt-4 inline-flex items-center rounded border border-emerald-400/40 ..."
>$ reset --filters</button>
```

`resetFilters()` sets all three properties back to `''`, the computed re-runs, and the full list returns. A dead-end empty state without an exit is a UX bug; pairing it with a reset action costs three lines.

## Further reading

- [Forms](https://livewire.laravel.com/docs/forms) — `wire:model` and its `live` / `debounce` modifiers
- [Actions](https://livewire.laravel.com/docs/actions) — `wire:click`, parameters, and magic actions like `$set`
- [wire:key](https://livewire.laravel.com/docs/wire-key) — keying loop elements for correct DOM diffing
