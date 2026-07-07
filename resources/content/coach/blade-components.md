---
title: "Views & Blade Components"
updated: "2026-07-07"
---

By the end of this walkthrough you'll have built the site's shared component set — the tag pill, the section heading, the branch badge — and the single slot-based layout every page renders into. Every code block is the real file from `resources/views/`.

## Create an anonymous component

```blade
<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded border border-ink-700/80 bg-ink-900 px-2 py-0.5 font-mono text-xs text-cyan-300']) }}>{{ $slot }}</span>
```

Save this one line as `resources/views/components/tag.blade.php` and it's immediately usable as `<x-tag>` — no PHP class, no registration. That's an *anonymous* component: when it only carries markup and display styling, the Blade file *is* the whole component. The `$attributes->merge()` call and `{{ $slot }}` are the next things you'll build up to.

## Add @props with defaults

```blade
@props(['prefix' => '//'])

<h2 {{ $attributes->merge(['class' => 'font-mono text-xl font-semibold text-ink-100 sm:text-2xl']) }}>
    <span class="mr-2 text-emerald-400">{{ $prefix }}</span>{{ $slot }}
</h2>
```

This is `resources/views/components/section-heading.blade.php` in full. `@props` declares which attributes the component accepts, and each declared prop becomes a variable — here `prefix` defaults to `'//'`, so most call sites just write `<x-section-heading>` and get the comment-style prefix for free, while `<x-section-heading prefix="##">` overrides it. Anything you *don't* declare in `@props` flows into `$attributes` instead — that split is what makes the next step work.

## Merge attributes for class composition

```blade
@props(['project', 'branch', 'repo' => null])

@php
    $classes = 'inline-flex items-center gap-1.5 rounded border border-ink-700/80 bg-ink-900 px-2 py-0.5 font-mono text-xs text-cyan-300';
@endphp

@if ($repo)
    <a href="{{ $repo }}" target="_blank" rel="noopener" {{ $attributes->merge(['class' => $classes.' transition-colors hover:border-ink-600 hover:text-cyan-200']) }}>
@else
    <span {{ $attributes->merge(['class' => $classes]) }}>
```

This is `resources/views/components/branch-badge.blade.php`, trimmed to its skeleton. `$attributes->merge()` combines the component's baseline classes with whatever the caller passes — so `<x-tag class="mt-2">` renders a span with *both* the pill classes and `mt-2`, merged rather than replaced. Here the badge shares one `$classes` string across two branches and appends hover styles only when `repo` makes it a link: props drive behavior, merging drives appearance.

## Fill the slot at the call site

```blade
@foreach ($this->post['tags'] as $tag)
    <x-tag>{{ $tag }}</x-tag>
@endforeach
```

This loop is from the log post page (`resources/views/components/⚡log-show.blade.php`). Whatever you place between a component's opening and closing tags arrives as `$slot` — each tag name here lands exactly where you wrote `{{ $slot }}` in step 1. Slots keep the component generic: `x-tag` renders *anything* as a pill, whether that's a tag name, a count, or nested markup.

## Build the slot-based layout

```blade
<main class="relative z-10 mx-auto w-full max-w-5xl flex-1 px-4 py-10 sm:px-6 sm:py-14">
    {{ $slot }}
</main>
```

The same slot mechanic scales up to the whole site: this `<main>` is from `resources/views/layouts/app.blade.php`, the layout every full-page Livewire component is injected into. The header, footer, and grid overlay live around it, and the page content lands in `{{ $slot }}`. The layout's `<head>` also reads `{{ $title ?? config('app.name') }}`, which is how each page sets its own browser title without ever touching the layout file.

## Compose a page from shared components

```blade
<x-branch-badge :project="$this->post['project']" :branch="$this->post['branch']" :repo="$this->post['repo']" />

@foreach ($this->post['tags'] as $tag)
    <x-tag>{{ $tag }}</x-tag>
@endforeach

<x-prose :html="$this->post['html']" />
```

Put it all together and a page is mostly component tags — these lines from `⚡log-show.blade.php` render the badge you built in step 3, the tag pills from step 1, and `x-prose`, which uses the same `@props` + merge pattern to wrap rendered Markdown in the site's typography classes. The `:` prefix passes each attribute as a PHP expression instead of a literal string, which is how the post's data reaches the badge's props. All of it renders into the layout's slot from step 5: one layout, a handful of small components, every page on the site.
