---
title: "Loading States & Skeletons"
updated: "2026-07-07"
---

By the end you'll have the log page's loading feedback built from scratch: skeleton cards that appear during search/filter roundtrips, the real list swapping out underneath them, and an emerald progress bar on `wire:navigate` page changes. Every block comes from `resources/views/components/⚡log-index.blade.php` and `config/livewire.php`.

## Add a skeleton block with wire:loading

```blade
{{-- Skeleton cards while a search/filter roundtrip is in flight --}}
<div wire:loading class="space-y-4" aria-hidden="true">
    @foreach (range(1, 3) as $i)
        <div class="animate-pulse rounded-lg border border-ink-800 bg-ink-900 p-5">
            <div class="h-3 w-20 rounded bg-ink-800"></div>
            <div class="mt-3 h-5 w-2/3 rounded bg-ink-800"></div>
            <div class="mt-3 h-3 w-full rounded bg-ink-850"></div>
        </div>
    @endforeach
</div>
```

Adding `wire:loading` to any element hides it by default (`display: none`) and shows it whenever a request is in flight to the server — no JavaScript to write. Note what's inside: the same `rounded-lg border border-ink-800 bg-ink-900 p-5` container as a real post card, with `animate-pulse` grey bars where the date, title, and excerpt will land. A skeleton works because it promises the right shape, so content can replace ghosts in place with no layout jump.

## Swap out the real list with wire:loading.remove

```blade
<div wire:loading.remove>
    <ul class="space-y-4">
        @foreach ($this->posts as $post)
            {{-- the real post cards --}}
        @endforeach
    </ul>
</div>
```

`wire:loading.remove` is the inverse of what you just wrote: visible by default, hidden during requests. Wrap the real post list in it, right below the skeleton block. Now every search keystroke and filter click swaps the two — skeletons in, list out — and Livewire flips them back when the response morphs in. Zero custom code so far.

## Add .delay so fast responses stay silent

```blade
<div wire:loading.delay class="space-y-4" aria-hidden="true">
    {{-- skeleton cards --}}
</div>

<div wire:loading.remove.delay>
    {{-- the real post list --}}
</div>
```

Without this, the skeletons flicker on every keystroke: locally a roundtrip takes ~50ms, and a flash of grey bars is worse than nothing. `.delay` makes the indicator appear only if the request takes over 200 milliseconds — fast responses come and go before it ever renders, while a production serverless cold start comfortably crosses the line and gets real feedback. Add it to both blocks so they keep swapping in sync. If 200ms is the wrong line, variants run from `.delay.shortest` (50ms) to `.delay.longest` (1s); and there's deliberately no `wire:target` here, since search and filters are the only roundtrips this component makes.

## Theme the navigate progress bar in config

```php
// config/livewire.php
'navigate' => [
    'show_progress_bar' => true,
    'progress_bar_color' => '#34d399',
],
```

Page-to-page moves via `wire:navigate` get their own indicator: Livewire's built-in progress bar. Its color is a config value rather than a CSS override, because Livewire injects its own styles after the app stylesheet — the supported knob wins where a cascade fight wouldn't. `#34d399` is the site's emerald accent, so the bar matches the theme.
