---
title: "Views & Blade Components"
category: laravel
order: 2
excerpt: "How anonymous Blade components, @props, attribute merging, and a slot-based layout compose every page on this site."
updated: "2026-07-06"
---

Blade components let you extract repeated markup into small, reusable pieces with a clean HTML-like syntax. This site uses *anonymous* components exclusively — components that are just a Blade template with no PHP class behind them. When a component only carries markup and a few display props, a class adds nothing; a single `.blade.php` file in `resources/views/components/` is the whole component.

## Anonymous components and @props

Any file in `resources/views/components/` is automatically usable as a tag: `section-heading.blade.php` becomes `<x-section-heading>`. The `@props` directive declares which attributes the component accepts, and lets you provide defaults. Here is `resources/views/components/section-heading.blade.php` in full:

```blade
@props(['prefix' => '//'])

<h2 {{ $attributes->merge(['class' => 'font-mono text-xl font-semibold text-ink-100 sm:text-2xl']) }}>
    <span class="mr-2 text-emerald-400">{{ $prefix }}</span>{{ $slot }}
</h2>
```

Because `prefix` defaults to `'//'`, most call sites write `<x-section-heading>Projects</x-section-heading>` and get the comment-style prefix for free; a page can override it with `<x-section-heading prefix="##">`. Anything declared in `@props` becomes a variable; anything *not* declared flows into `$attributes` instead.

## Merging attributes

`$attributes->merge()` is what makes small components composable. It combines the component's own classes with whatever the caller passes, so the component defines its baseline look and the call site adds context-specific tweaks. The tag pill in `resources/views/components/tag.blade.php` is one line, and merging is doing all the work:

```blade
<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded border border-ink-700/80 bg-ink-900 px-2 py-0.5 font-mono text-xs text-cyan-300']) }}>{{ $slot }}</span>
```

Writing `<x-tag class="mt-2">livewire</x-tag>` renders a span with *both* the baseline pill classes and `mt-2` — merged, not replaced. The same pattern powers `resources/views/components/prose.blade.php`, which wraps rendered Markdown in the site's typography styles, and `resources/views/components/branch-badge.blade.php`, which shares one class string across two rendering branches:

```blade
@props(['project', 'branch', 'repo' => null])

@php
    $classes = 'inline-flex items-center gap-1.5 rounded border border-ink-700/80 bg-ink-900 px-2 py-0.5 font-mono text-xs text-cyan-300';
@endphp

@if ($repo)
    <a href="{{ $repo }}" target="_blank" rel="noopener" {{ $attributes->merge(['class' => $classes.' transition-colors hover:border-ink-600 hover:text-cyan-200']) }}>
```

When `repo` is present the badge becomes a link with hover styles appended; otherwise it renders as a plain span with the same base classes. Props drive behavior, merging drives appearance.

## Slots

Content placed between a component's opening and closing tags arrives as `$slot`. You saw it in both components above — `{{ $slot }}` is where `<x-tag>livewire</x-tag>` puts the word "livewire". Slots keep the component generic: `x-tag` renders *anything* as a pill, whether that is a tag name, a count, or nested markup.

## The layout is a component too

The entire site renders into a single slot. `resources/views/layouts/app.blade.php` is the layout every full-page Livewire component is injected into — the header, footer, and grid overlay live here, and the page content lands in `{{ $slot }}`:

```blade
<main class="relative z-10 mx-auto w-full max-w-5xl flex-1 px-4 py-10 sm:px-6 sm:py-14">
    {{ $slot }}
</main>
```

The layout also reads `$title ?? config('app.name')` in its `<head>`, which is how each page component sets its own browser title without touching the layout. One slot, one layout file, and every page on the site inherits the same shell.

## Further reading

- [Blade Templates](https://laravel.com/docs/13.x/blade) — components, `@props`, attributes, and slots
