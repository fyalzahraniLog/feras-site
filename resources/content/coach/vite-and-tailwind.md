---
title: "Asset Bundling with Vite & Tailwind 4"
updated: "2026-07-07"
---

By the end you'll have the site's entire asset pipeline: Vite bundling CSS and JS through the Laravel plugin, Tailwind 4 themed entirely in CSS, and both fonts self-hosted. Two files carry all of it — `vite.config.js` and `resources/css/app.css`.

## Configure Vite with the Laravel plugin

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
```

This is `vite.config.js`. The `input` array declares your two entry points — the only files Vite is told to bundle; everything else gets pulled in through imports. `refresh: true` makes the browser do a full reload whenever a Blade view changes, so editing a template feels as immediate as editing CSS. `tailwindcss()` is Tailwind 4's first-party Vite plugin — no PostCSS config needed.

## Load the bundles with @vite

```blade
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
```

In `resources/views/layouts/app.blade.php`, you never reference a built file by name — production filenames contain content hashes that change on every build. The `@vite` directive takes the same entry points you declared in step 1 and does the right thing per environment: in development it injects the dev server's hot-reloading client; in production it reads the build manifest and emits `<link>` and `<script>` tags pointing at the hashed files. One line, and cache busting is never your problem again.

## Theme Tailwind 4 with @theme

```css
@import 'tailwindcss';

@plugin '@tailwindcss/typography';

@theme {
    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
    --font-mono: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;

    /* "ink" — the dark blue-black scale the whole site is built on */
    --color-ink-100: #e2e8f0;
    --color-ink-300: #a7b3c4;
    --color-ink-500: #64748b;
    --color-ink-700: #2a3646;
    --color-ink-800: #1c2430;
    --color-ink-900: #10151d;
    --color-ink-950: #0a0d12;
}
```

This is the top of `resources/css/app.css` — the CSS entry point from step 1. Tailwind 4 has no `tailwind.config.js`: design tokens live in the `@theme` block as CSS custom properties, and declaring `--color-ink-950` is all it takes for `bg-ink-950`, `text-ink-950`, and every other color utility to exist. The layout's body tag uses them directly: `bg-ink-950 font-sans text-ink-200`. Even plugins load from CSS — the `@plugin` line enables the `prose` classes that style rendered Markdown.

## Self-host the fonts with bunny()

```js
import { bunny } from 'laravel-vite-plugin/fonts';

laravel({
    input: ['resources/css/app.css', 'resources/js/app.js'],
    refresh: true,
    fonts: [
        bunny('Instrument Sans', { weights: [400, 500, 600] }),
        bunny('JetBrains Mono', { weights: [400, 500, 700] }),
    ],
}),
```

Back in `vite.config.js`, add a `fonts` array to the Laravel plugin from step 1. The `bunny()` helper downloads each typeface from the privacy-friendly Bunny Fonts service at build time, bundles the files into your assets, and emits the `@font-face` rules — visitors never make a request to a font CDN. The `@theme` block from step 3 just references the families by name, and now those names resolve.

## Build for production

```shell
npm run build
```

This runs `vite build`, which writes hashed, minified files plus a `manifest.json` into `public/build/`. That manifest is what the `@vite` directive from step 2 reads in production to emit the right tags — the two ends of the pipeline meet here. On this site, a passing `npm run build` is required before committing any asset change.
