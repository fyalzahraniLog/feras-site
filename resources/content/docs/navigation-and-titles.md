---
title: "SPA Navigation & Page Titles"
category: livewire
order: 4
excerpt: "How wire:navigate makes page changes feel instant, and how each page sets its <title> statically or dynamically."
updated: "2026-07-06"
---

This site is a set of server-rendered pages, yet moving between them feels like a single-page app. Two Livewire features make that work: `wire:navigate` for the transitions, and page titles that flow from each component into the shared layout — statically via an attribute, or dynamically via a render hook.

## wire:navigate

A normal link tears down the whole page: the browser re-fetches CSS and JS, re-parses everything, and repaints from white. Adding `wire:navigate` to a link tells Livewire to intercept the click, fetch the new page in the background, and swap only the page content — assets stay warm and the transition is near-instant.

Every internal link on the docs pages carries it. The sidebar links in `resources/views/components/⚡docs-show.blade.php` look like this:

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

The `href` is still a real URL, so the link works without JavaScript, is crawlable, and can be opened in a new tab — `wire:navigate` is purely progressive enhancement. The doc cards in `⚡docs-index.blade.php` and the breadcrumb and footer links back to the index use the same directive, so the whole DOC section navigates without a full reload.

## Static titles with #[Title]

For pages whose title never changes, Livewire provides the `#[Title]` attribute directly on the component class. The docs index in `resources/views/components/⚡docs-index.blade.php` declares its title in one line:

```php
use Livewire\Attributes\Title;

new #[Title('DOC — Feras')] class extends Component
{
    public string $search = '';
    // ...
};
```

That string becomes the document's `<title>` when the page renders — including when you arrive via `wire:navigate`, which updates the title as part of the swap.

## Dynamic titles with the rendering() hook

A doc page's title depends on which doc you're reading, so an attribute with a fixed string won't do. Attributes can't contain runtime expressions — instead, the component uses the `rendering()` lifecycle hook, which Livewire calls right after the view is created but before it renders. In `⚡docs-show.blade.php`:

```php
public function rendering($view): void
{
    $view->title($this->doc['title'].' — DOC — Feras');
}
```

The hook receives the view instance, and `$view->title()` sets the page title from live data — here the `doc` computed property, which is derived from the `slug` route parameter. `⚡log-show.blade.php` does the same for log posts:

```php
public function rendering($view): void
{
    $view->title($this->post['title'].' — Log Dev — Feras');
}
```

Because the computed property is cached per request, calling `$this->doc` in the hook costs nothing extra even though the template reads it again moments later.

## How the title reaches the layout

Both mechanisms end in the same place. Full-page components render inside `resources/views/layouts/app.blade.php`, and Livewire passes the title into that layout as the `$title` variable:

```blade
<title>{{ $title ?? config('app.name') }}</title>
```

If a component sets a title — via `#[Title]` or `$view->title()` — it lands here; if not, the layout falls back to the app name. That single line is the only title markup on the site: components decide *what* the title is, the layout decides *where* it goes. When you add a new page, pick the mechanism that matches your data — `#[Title]` for a fixed string, `rendering()` when the title comes from a route parameter or repository lookup.

## Further reading

- [Navigate](https://livewire.laravel.com/docs/navigate) — `wire:navigate`, prefetching, and asset handling
- [Pages](https://livewire.laravel.com/docs/pages) — page titles and layout resolution
