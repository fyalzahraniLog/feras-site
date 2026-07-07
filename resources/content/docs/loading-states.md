---
title: "Loading States & Skeletons"
category: livewire
order: 5
excerpt: "How the log and docs pages show skeleton cards during server roundtrips, and why the indicators only appear on slow responses."
updated: "2026-07-07"
---

Every search keystroke and filter click on this site is a server roundtrip — Livewire re-renders the component on the server and morphs the result into the page. Locally that takes ~50ms and needs no feedback; in production, a cold serverless function can take a second. This page documents how the site shows **skeleton cards** during those waits, and an emerald progress bar during `wire:navigate` page changes.

## wire:loading toggles visibility

Per the official docs, "adding `wire:loading` to any element will hide it by default (using `display: none` in CSS) and show it when a request is sent to the server." No JavaScript to write — Livewire flips the element's visibility around each request's lifecycle.

The log page pairs two blocks: a skeleton that appears *during* requests, and the real list that hides. From `resources/views/components/⚡log-index.blade.php`:

```blade
{{-- Skeleton cards while a search/filter roundtrip is in flight --}}
<div wire:loading.delay class="space-y-4" aria-hidden="true">
    @foreach (range(1, 3) as $i)
        <div class="animate-pulse rounded-lg border border-ink-800 bg-ink-900 p-5">
            <div class="h-3 w-20 rounded bg-ink-800"></div>
            <div class="mt-3 h-5 w-2/3 rounded bg-ink-800"></div>
            ...
        </div>
    @endforeach
</div>

<div wire:loading.remove.delay>
    {{-- the real post list --}}
</div>
```

`wire:loading.remove` is the inverse: visible by default, hidden during requests. Together the two blocks swap — skeletons in, list out — with zero custom code.

## The skeletons mirror the real cards

A skeleton works because it *promises the right shape*: same `rounded-lg border border-ink-800 bg-ink-900 p-5` container as a real post card, with `animate-pulse` grey bars where the date, title, and tags will land. The docs index does the same with a `sm:grid-cols-2` grid of four card ghosts, matching its layout. When the response arrives, content replaces ghosts in place — no layout jump.

## .delay: only show indicators when they help

The `.delay` modifier means the skeleton "will only appear if the request takes over 200 milliseconds." That single modifier is why the skeletons don't flicker on every keystroke locally: fast responses come and go before the indicator ever renders, while a production cold start comfortably crosses the threshold and gets real feedback. Variants from `.delay.shortest` (50ms) to `.delay.longest` (1s) exist when 200ms isn't the right line.

There's no `wire:target` on these blocks on purpose — the log and docs components only make roundtrips for search and filters, so scoping would add noise without changing behavior. Add `wire:target="actionName"` when a component has *several* actions and only some deserve an indicator.

## The navigate progress bar

Page-to-page moves via `wire:navigate` get their own indicator: Livewire's built-in progress bar. Its color is a config value, not a CSS override — Livewire injects its own styles after the app stylesheet, so the supported knob wins where a cascade fight wouldn't. From `config/livewire.php`:

```php
'navigate' => [
    'show_progress_bar' => true,
    'progress_bar_color' => '#34d399',
],
```

## Further reading

- [wire:loading](https://livewire.laravel.com/docs/wire-loading) — visibility toggling, `.remove`, `.delay` variants, and `wire:target` scoping
