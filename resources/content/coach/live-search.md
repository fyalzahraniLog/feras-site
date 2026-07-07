---
title: "Live Search & Filtering"
updated: "2026-07-07"
---

By the end of this walkthrough you'll have built the log page's live search: a debounced input bound to a Livewire property, a computed property that filters posts on every render, a keyed result loop, and an empty state with a one-click reset. Every snippet is the real code from `resources/views/components/⚡log-index.blade.php`, trimmed to the search path — the doc page shows the full three-filter version.

## Add the search input

```blade
<label class="flex items-center gap-3 rounded-lg border border-ink-800 bg-ink-900 px-4 py-2.5 transition-colors focus-within:border-emerald-400/40 focus-within:ring-2 focus-within:ring-emerald-400/40">
    <span class="font-mono text-sm text-emerald-400" aria-hidden="true">$</span>
    <input
        type="text"
        placeholder="grep posts..."
        aria-label="Search posts"
        class="w-full bg-transparent font-mono text-sm text-ink-100 placeholder-ink-500 focus:outline-none"
    />
</label>
```

Start with plain markup in the single-file component `resources/views/components/⚡log-index.blade.php` — a `$`-prompt styled like the rest of the terminal theme, wrapping an ordinary text input. Right now it's dead: typing into it changes nothing. The next two steps connect it to the component's state.

## Bind it with wire:model.live

```php
public string $search = '';
```

Declare a public property on the component class — a scalar, per this site's rule that public properties never hold Collections. Then point the input at it:

```blade
<input
    type="text"
    wire:model.live="search"
    placeholder="grep posts..."
/>
```

Plain `wire:model` only syncs on events like `change` — you'd have to tab away to see results. The `live` modifier sends updates to the server as you type, which is what search-as-you-type needs.

## Debounce the keystrokes

```blade
<input
    type="text"
    wire:model.live.debounce.300ms="search"
    placeholder="grep posts..."
    aria-label="Search posts"
    class="w-full bg-transparent font-mono text-sm text-ink-100 placeholder-ink-500 focus:outline-none"
/>
```

`live` alone means one network request per keystroke. Chaining `debounce.300ms` waits until you pause typing for 300 milliseconds before sending, so a quickly typed word costs one request instead of eight. This is the exact binding the input ships with — `⚡docs-index.blade.php` uses the identical one for the docs search.

## Filter inside a computed property

```php
use Livewire\Attributes\Computed;

#[Computed]
public function posts()
{
    return app(\App\Content\ContentRepository::class)
        ->posts()
        ->filter(function (array $post) {
            if ($this->search !== ''
                && stripos($post['title'], $this->search) === false
                && stripos($post['excerpt'], $this->search) === false) {
                return false;
            }

            return true;
        })
        ->values();
}
```

Notice the search never touches a post list directly — it only changes `$search`. The list is derived here: a computed property re-evaluates on every render, so each debounced update automatically produces a freshly filtered Collection, matched case-insensitively with `stripos` against title and excerpt. On the full log page the tag and time-window filters compose into this same filter pass — the state driving them stays scalar either way.

## Key the result loop

```blade
<ul class="space-y-4">
    @foreach ($this->posts as $post)
        <li wire:key="post-{{ $post['slug'] }}">
            ...
        </li>
    @endforeach
</ul>
```

Render the computed list, and give every loop item a stable, unique `wire:key`. When a filtered list changes, Livewire diffs the new HTML against the old — keys let it track elements by identity rather than position. Without them, filtering can make Livewire reuse the wrong DOM nodes: an open `<details>` element staying open on the wrong post, for example.

## Show an empty state with a reset action

```blade
@if ($this->posts->isEmpty())
    <div class="rounded-lg border border-ink-800 bg-ink-900 p-6 text-center">
        <p class="font-mono text-sm text-ink-400">
            no results for '<span class="text-ink-200">...</span>'
        </p>
        <button
            type="button"
            wire:click="resetFilters"
            class="mt-4 inline-flex items-center rounded border border-emerald-400/40 bg-emerald-400/10 px-3 py-1 font-mono text-xs text-emerald-300 transition-colors hover:bg-emerald-400/20 focus:outline-none focus:ring-2 focus:ring-emerald-400/40"
        >$ reset --filters</button>
    </div>
@else
    {{-- the keyed list from the previous step --}}
@endif
```

When everything is filtered out, show what failed and offer a way back — a dead-end empty state without an exit is a UX bug. The button is wired to a component method with `wire:click`, and for our search-only build the action is one assignment:

```php
public function resetFilters(): void
{
    $this->search = '';
}
```

Clearing the state makes the computed re-run and the full list return. On the real log page `resetFilters()` sets all three filter properties back to `''` — `$activeTag` and `$window` belong to the tag chips and time-window chips the full page adds on top of this search box, same pattern, covered on the doc page. (The real empty state also echoes the failing filter in place of the `...`.)
