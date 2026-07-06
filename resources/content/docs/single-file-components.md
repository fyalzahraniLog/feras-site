---
title: "Single-File Components"
category: livewire
order: 1
excerpt: "How this site keeps each page's PHP logic and Blade template together in one ⚡-prefixed file."
updated: "2026-07-06"
---

Every page on this site is a Livewire single-file component: one `.blade.php` file that contains both the PHP class and the template it renders. There is no `app/Livewire` directory here at all. When a page is small enough that its logic and markup are really one thing, splitting them across two files just makes you jump around. The single-file format keeps them side by side.

By convention on this site, these files start with the ⚡ emoji — `⚡home.blade.php`, `⚡log-index.blade.php` — so you can tell at a glance which files in `resources/views/components/` are Livewire pages and which are plain Blade components like `tag.blade.php`.

## The single-file format

A single-file component is an anonymous PHP class extending `Livewire\Component`, declared at the top of the file, followed by the template. Here is the top of `resources/views/components/⚡home.blade.php`:

```php
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;

new #[Title('Feras Alzahrani — Full-Stack Developer')] class extends Component
{
    #[Computed]
    public function posts()
    {
        return app(\App\Content\ContentRepository::class)->posts()->take(3);
    }
};
```

The `new` keyword returns the anonymous class instance, and everything after the closing `?>` is the component's Blade view. Public properties and methods on the class are available in the template exactly as they would be in a class-based component.

## Routing to a component by name

You don't need a controller to serve one of these pages. In `routes/web.php`, `Route::livewire()` maps a URL straight to a component name:

```php
Route::livewire('/', 'home')->name('home');

Route::livewire('/log', 'log-index')->name('log.index');
Route::livewire('/log/{slug}', 'log-show')->name('log.show');
```

The second argument is the component name, which Livewire resolves to the matching view file — `'log-show'` finds `⚡log-show.blade.php`. The full page renders through the component, so the whole route is defined in one line.

## Receiving route parameters in mount()

When a route has a parameter, Livewire passes it to the component's `mount()` method, matched by name. The `{slug}` segment from `/log/{slug}` arrives as the `$slug` argument in `resources/views/components/⚡log-show.blade.php`:

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

`mount()` runs once, when the component first loads. This is the place to store route input on a public property and to bail out early — here, aborting with a 404 when no post matches the slug. Note that only the scalar `$slug` string is stored; the post itself is derived later in a computed property (see [Computed Properties](/docs/computed-properties) for why).

## One root element

Livewire tracks each component in the DOM, so every component template must have exactly one root element. Each page on this site wraps its content in a single `<div>` or `<article>` — `⚡log-show.blade.php` uses one `<article class="space-y-8">` around the back link, header, prose, and navigation. If you add a second top-level element, Livewire's DOM diffing breaks in ways that are painful to debug, so keep the wrapper even when it feels redundant.

## The automatic layout

Full-page components render inside a layout file. Livewire looks for `resources/views/layouts/app.blade.php` by default — which is exactly where this site keeps its shell (terminal-style header, footer, and a `{{ $slot }}` where the component's output lands). Because the default location is used, no component here needs a `#[Layout]` attribute; every page picks up the shared chrome automatically.

## Further reading

- [Components](https://livewire.laravel.com/docs/components) — single-file components, routing, and the one-root-element rule
- [Pages](https://livewire.laravel.com/docs/pages) — full-page components, route parameters, and layouts
