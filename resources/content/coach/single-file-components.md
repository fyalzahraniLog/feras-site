---
title: "Single-File Components"
updated: "2026-07-07"
---

By the end of this walkthrough you'll have built a full Livewire page the way every page on this site is built: one ⚡-prefixed file holding both the PHP class and its template, mapped to a URL with a single route line. You'll build the homepage component first, then reuse the same pattern to receive a route parameter on the log-show page.

## Create the ⚡ component file

```php
<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
```

Create `resources/views/components/⚡home.blade.php` — yes, the filename literally starts with the ⚡ emoji. That prefix is this site's convention for spotting Livewire pages at a glance among plain Blade components like `tag.blade.php`; there is no `app/Livewire` directory here at all. The file opens with `<?php` and imports everything the class will need.

## Declare the anonymous class

```php
new #[Title('Feras Alzahrani — Full-Stack Developer')] class extends Component
{
    #[Computed]
    public function posts()
    {
        return app(\App\Content\ContentRepository::class)->posts()->take(3);
    }
};
?>
```

Right below the imports, `new` declares an anonymous class extending `Livewire\Component` and returns its instance — that's the whole component class, no separate file. Its public properties and methods work in the template exactly as they would in a class-based component. The closing `?>` ends the PHP half of the file; everything after it is the Blade view.

## Write the single-root template

```blade
<div class="space-y-20 py-10 sm:space-y-24">

    <section>
        {{-- Fake terminal window --}}
    </section>

    {{-- ...more sections... --}}

</div>
```

The template must have exactly one root element — Livewire tracks each component in the DOM, and a second top-level element breaks its diffing in ways that are painful to debug, so keep the wrapper even when it feels redundant. Here it's a single `<div>`; `⚡log-show.blade.php` uses one `<article class="space-y-8">`. The output lands in the `{{ $slot }}` of `resources/views/layouts/app.blade.php` automatically — that's Livewire's default layout location, so no `#[Layout]` attribute is needed.

## Register the route

```php
Route::livewire('/', 'home')->name('home');

Route::livewire('/log', 'log-index')->name('log.index');
Route::livewire('/log/{slug}', 'log-show')->name('log.show');
```

In `routes/web.php`, `Route::livewire()` maps a URL straight to a component by name — no controller. Livewire resolves the second argument to the matching view file: `'home'` finds the `⚡home.blade.php` you just built, `'log-show'` finds `⚡log-show.blade.php`. The full page renders through the component, so the whole route is one line.

## Receive the route parameter in mount()

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

The `/log/{slug}` route you just registered has a parameter, and Livewire passes it to `mount()` matched by name — this is the class body of `⚡log-show.blade.php`. `mount()` runs once, when the component first loads, so it's the place to store route input on a public property and bail out early with a 404 when nothing matches. Notice only the scalar `$slug` string is stored; the post itself is derived later in a computed property.
