---
title: "Asset Bundling with Vite & Tailwind 4"
category: laravel
order: 4
excerpt: "How this site bundles CSS and JS with Vite, themes Tailwind 4 in pure CSS, and self-hosts its fonts through the Laravel plugin."
updated: "2026-07-06"
---

Laravel pairs with Vite to bundle your CSS and JavaScript: instant hot reloading in development, hashed and minified files in production. This site adds Tailwind CSS 4 on top, which moves *all* configuration into the CSS file itself — there is no `tailwind.config.js` anywhere in the project. Two files define the entire asset pipeline: `vite.config.js` and `resources/css/app.css`.

## The @vite directive

Blade templates never reference built assets by filename, because production filenames contain content hashes that change on every build. Instead, the layout in `resources/views/layouts/app.blade.php` uses the `@vite` directive:

```blade
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

In development, this injects the Vite dev server's hot-reloading client. In production, it reads the `manifest.json` that `vite build` writes into `public/build/` and emits `<link>` and `<script>` tags pointing at the hashed files. The same one-line directive serves both environments — you never think about cache busting again.

The corresponding entry points are declared in `vite.config.js` via the Laravel plugin:

```js
laravel({
    input: ['resources/css/app.css', 'resources/js/app.js'],
    refresh: true,
    // ...
}),
tailwindcss(),
```

`refresh: true` makes the browser do a full reload whenever a Blade view changes, so editing a component template is as immediate as editing CSS. The `tailwindcss()` plugin is Tailwind 4's first-party Vite integration — no PostCSS configuration required.

## CSS-first configuration with @theme

Tailwind 4 replaces the JavaScript config file with directives in your stylesheet. Design tokens are declared in an `@theme` block as CSS custom properties, and Tailwind generates utilities from them. This is where the site's custom "ink" palette — the dark blue-black scale everything is built on — lives, in `resources/css/app.css`:

```css
@import 'tailwindcss';

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

Declaring `--color-ink-950` is all it takes for `bg-ink-950`, `text-ink-950`, `border-ink-950`, and every other color utility to exist. The body tag in the layout uses them directly: `bg-ink-950 font-sans text-ink-200`.

## Custom CSS with @layer

Styles that are not utilities go into Tailwind's cascade layers, so they slot in at the right specificity. The site uses `@layer base` for element-level defaults (a tinted `::selection` color) and `@layer components` for the blueprint grid that floats behind every page:

```css
@layer components {
    /* Faint blueprint grid behind the page, fading out toward the bottom */
    .bg-grid-overlay {
        background-image:
            linear-gradient(to right, rgba(42, 54, 70, 0.22) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(42, 54, 70, 0.22) 1px, transparent 1px);
        background-size: 32px 32px;
        mask-image: radial-gradient(ellipse 90% 65% at 50% 0%, black 25%, transparent 75%);
    }
}
```

Because it lives in the components layer, a utility class can still override it from the markup when needed.

## Self-hosted fonts with bunny()

Rather than loading Google Fonts from a third-party CDN, the site self-hosts its two typefaces through the Laravel Vite plugin's fonts feature. In `vite.config.js`:

```js
import { bunny } from 'laravel-vite-plugin/fonts';

fonts: [
    bunny('Instrument Sans', { weights: [400, 500, 600] }),
    bunny('JetBrains Mono', { weights: [400, 500, 700] }),
],
```

The `bunny()` helper downloads the font files from the privacy-friendly Bunny Fonts service at build time, bundles them into your assets, and emits the `@font-face` rules — visitors never make a request to a font CDN. The `@theme` block above then simply references the families by name.

## The typography plugin

Rendered Markdown needs styled headings, lists, and code blocks that plain utilities cannot reach. Tailwind 4 loads plugins from CSS too — `resources/css/app.css` pulls in the official typography plugin with a single directive:

```css
@plugin '@tailwindcss/typography';
```

That enables the `prose` classes used by `resources/views/components/prose.blade.php`, which wraps every doc page and log post — including this one — in `prose prose-invert` plus ink-palette overrides like `prose-p:text-ink-300` and `prose-code:text-emerald-300`.

## Further reading

- [Asset Bundling (Vite)](https://laravel.com/docs/13.x/vite) — the `@vite` directive, entry points, and advanced configuration
- [Blade Templates](https://laravel.com/docs/13.x/blade) — where directives like `@vite` fit into your layouts
