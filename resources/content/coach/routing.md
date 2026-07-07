---
title: "Routing with Route::livewire"
updated: "2026-07-07"
---

You're going to wire a URL straight to a full-page Livewire component — no controller in between. By the end you'll have the log section of this site routed exactly as it is in `routes/web.php`: a listing page, a detail page with a `{slug}` parameter, and a real 404 for slugs that don't exist.

## Register the route

```php
use Illuminate\Support\Facades\Route;

Route::livewire('/log', 'log-index');
```

`Route::livewire()` registers a URL that renders a Livewire component as the entire page. The component name `'log-index'` maps to the single-file component `resources/views/components/⚡log-index.blade.php` — the filenames really begin with the ⚡ emoji, Livewire's marker for single-file components. That one line replaces a controller, a controller method, and a view: the component *is* the page.

## Name it and link with route()

```php
Route::livewire('/log', 'log-index')->name('log.index');
```

Chaining `name()` decouples your templates from your URL structure: if `/log` ever becomes `/journal`, only this line changes and every link keeps working. Templates generate URLs with the `route()` helper — here's the layout's navigation in `resources/views/layouts/app.blade.php`:

```blade
<a href="{{ route('log.index') }}" class="{{ $navLink(request()->routeIs('log.*')) }}">./log-dev</a>
```

The dotted naming convention pays off in `request()->routeIs('log.*')` — the wildcard matches every route in the section, which is how the nav stays highlighted on both the listing page and individual posts.

## Add the {slug} parameter

```php
Route::livewire('/log/{slug}', 'log-show')->name('log.show');
```

Individual posts need a second route, and `{slug}` is a route parameter — a placeholder that captures whatever sits in that URL segment. Visiting `/log/hello-world` matches this route with `slug` set to `'hello-world'`. Note the name follows the same dotted convention, so the `log.*` wildcard from the previous step already covers it.

## Receive the slug in mount()

```php
public string $slug = '';

public function mount(string $slug): void
{
    $this->slug = $slug;
}
```

Inside `resources/views/components/⚡log-show.blade.php`, Livewire passes the route parameter to `mount()`, matching by argument name — exactly the way route model binding injects into controller methods. `mount()` runs once, when the component first loads, so storing the slug on a public property keeps it available for the rest of the request. The component's computed `post()` method uses it to fetch the content on demand.

## Abort with 404 for unknown slugs

```php
public function mount(string $slug): void
{
    $this->slug = $slug;

    if (app(\App\Content\ContentRepository::class)->post($slug) === null) {
        abort(404);
    }
}
```

This site's content lives in Markdown files, not a database, so there's no model binding to fail automatically for a missing record. Instead you ask the repository yourself and call `abort(404)` when nothing matches — it throws an HTTP exception that Laravel's handler converts into a proper 404 response, the same mechanism `findOrFail()` uses under the hood. Request an unknown slug and the browser gets a real 404 page, not an empty component.
