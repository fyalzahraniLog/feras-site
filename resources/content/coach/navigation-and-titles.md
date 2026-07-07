---
title: "SPA Navigation & Page Titles"
updated: "2026-07-07"
---

By the end of this walkthrough you'll have links that swap pages without a full reload, and every page setting its own `<title>` — statically or from live data. It's the exact setup running on this site's DOC section.

## Add wire:navigate to your links

```blade
<a
    href="{{ route('docs.show', $doc['slug']) }}"
    wire:navigate
    @class([
        'block border-l-2 py-1 pl-3 text-sm transition-colors',
        'border-emerald-400 bg-emerald-400/10 text-emerald-300' => $doc['slug'] === $slug,
        'border-transparent text-ink-400 hover:text-ink-100' => $doc['slug'] !== $slug,
    ])
>
    {{ $doc['title'] }}
</a>
```

Start with an ordinary anchor and add one attribute: `wire:navigate`. Livewire now intercepts the click, fetches the new page in the background, and swaps only the page content — CSS and JS stay warm instead of being re-fetched and re-parsed from white. The `href` is still a real URL, so the link works without JavaScript and opens fine in a new tab; this is pure progressive enhancement. This is the sidebar link from `resources/views/components/⚡docs-show.blade.php` — every internal link in the DOC section carries the same directive.

## Declare a static title with #[Title]

```php
use Livewire\Attributes\Title;

new #[Title('DOC — Feras')] class extends Component
{
    public string $search = '';
    // ...
};
```

Your links now swap pages, so each page needs to bring its own `<title>` along. For a page whose title never changes, put the `#[Title]` attribute directly on the component class — this is the docs index in `resources/views/components/⚡docs-index.blade.php`. That string becomes the document title when the page renders, including when you arrive via `wire:navigate`: updating the title is part of the swap.

## Set dynamic titles in the rendering() hook

```php
public function rendering($view): void
{
    $view->title($this->doc['title'].' — DOC — Feras');
}
```

A doc page's title depends on which doc you're reading, and attributes can't contain runtime expressions — so `#[Title]` won't do here. Add the `rendering()` lifecycle hook instead: Livewire calls it right after the view is created but before it renders, handing you the view instance so `$view->title()` can set the title from live data. Here `$this->doc` is a computed property derived from the `slug` route parameter, and because computed properties are cached per request, reading it again in the template costs nothing extra. This is `⚡docs-show.blade.php`; `⚡log-show.blade.php` does the same for log posts.

## Wire the title into the layout

```blade
<title>{{ $title ?? config('app.name') }}</title>
```

Both mechanisms end here. Full-page components render inside `resources/views/layouts/app.blade.php`, and Livewire passes whatever the component set — via `#[Title]` or `$view->title()` — into the layout as the `$title` variable, falling back to the app name if nothing was set. This single line is the only title markup on the site: components decide *what* the title is, the layout decides *where* it goes. When you add a new page, pick `#[Title]` for a fixed string, `rendering()` when the title comes from a route parameter or repository lookup.

## Re-initialize your JS on livewire:navigated

```js
import { initBlackhole } from './blackhole';

initBlackhole();

document.addEventListener('livewire:navigated', () => initBlackhole());
```

One consequence of assets staying warm: your bundle runs once, on the first full load — a `wire:navigate` swap won't re-run it. So any script that sets up the page listens for the `livewire:navigated` event, which fires after every swap, and runs its init again. That's `resources/js/app.js` re-initializing the black-hole canvas: called once on load, then once per navigation. Follow the same pattern for anything else that binds to fresh DOM.
