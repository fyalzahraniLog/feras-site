---
title: "Routing with Route::livewire"
category: laravel
order: 1
excerpt: "How every URL on this site maps straight to a Livewire component, with named routes, slug parameters, and 404s from mount()."
updated: "2026-07-06"
---

Most Laravel applications route requests to controllers. This site skips that layer entirely: every URL resolves directly to a full-page Livewire component. There are no controllers in the codebase — the component *is* the page, holding both the logic and the view in a single file. For a small content site, that removes an entire class of boilerplate while keeping everything Laravel gives you: named routes, route parameters, and middleware.

## Registering component routes

The `Route::livewire()` method registers a URL that renders a Livewire component as the entire page. The component name maps to a file in `resources/views/components/` — for example, `'home'` resolves to the `⚡home.blade.php` single-file component (yes, the filenames really begin with the ⚡ emoji, Livewire's marker for single-file components).

The entire routes file, `routes/web.php`, looks like this:

```php
use Illuminate\Support\Facades\Route;

Route::livewire('/', 'home')->name('home');

Route::livewire('/log', 'log-index')->name('log.index');
Route::livewire('/log/{slug}', 'log-show')->name('log.show');

Route::livewire('/docs', 'docs-index')->name('docs.index');
Route::livewire('/docs/{slug}', 'docs-show')->name('docs.show');
```

Five routes, five components, zero controllers. That is the whole routing surface of the site.

## Named routes

Every route above receives a name via `name()`. Naming routes decouples your templates from your URL structure: if `/log` ever becomes `/journal`, only the route definition changes — every link keeps working. Templates generate URLs with the `route()` helper. The layout's navigation in `resources/views/layouts/app.blade.php` uses names exclusively:

```blade
<a href="{{ route('home') }}" class="{{ $navLink(request()->routeIs('home')) }}">./about</a>
<a href="{{ route('log.index') }}" class="{{ $navLink(request()->routeIs('log.*')) }}">./log-dev</a>
<a href="{{ route('docs.index') }}" class="{{ $navLink(request()->routeIs('docs.*')) }}">./doc</a>
```

Notice `request()->routeIs('log.*')` — the dotted naming convention (`log.index`, `log.show`) lets a wildcard match every route in a section, which is how the active nav state works on both the listing page and individual posts.

## Route parameters and mount()

When a route contains a parameter like `{slug}`, Livewire passes it to the component's `mount()` method, matching by argument name — exactly the way route model binding injects into controller methods. Visiting `/log/hello-world` calls `mount(slug: 'hello-world')`.

The log post component in `resources/views/components/⚡log-show.blade.php` receives the slug this way:

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

`mount()` runs once, when the component first loads. Storing the slug on a public property makes it available for the rest of the request — the component's computed `post()` method uses it to fetch the content on demand.

## Returning 404 from a component

Because this site's content lives in Markdown files rather than a database, there is no model binding to fail automatically for a missing record. Instead, `mount()` checks the repository itself and calls `abort(404)` when no post matches the slug, as shown above. The `abort()` helper throws an HTTP exception that Laravel's handler converts into a proper 404 response — the same mechanism `findOrFail()` uses under the hood. The result is identical from the browser's perspective: request an unknown slug, get a real 404 page, not an empty component.

## Further reading

- [Routing](https://laravel.com/docs/13.x/routing) — route registration, parameters, and named routes
- [HTTP Responses](https://laravel.com/docs/13.x/responses) — `abort()` and error responses
