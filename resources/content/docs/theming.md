---
title: "Theming with CSS Variables"
category: laravel
order: 5
excerpt: "How five complete dark themes remap the whole design system through Tailwind 4 variables — without touching a single template."
updated: "2026-07-07"
---

This site ships five dark themes — **ink**, **ember**, **nebula**, **ocean**, and **crimson** — switched from the header dots. The remarkable part is what the implementation *didn't* require: no template changes, no duplicate stylesheets, no theme-aware components. The whole mechanism is a property of how Tailwind 4 works, and adding a theme is ~20 lines of CSS variables plus one entry in the switcher's list.

## Utilities are variables underneath

In Tailwind 4, design tokens are defined in CSS with the `@theme` directive, and — per the official docs — "Tailwind also generates regular CSS variables for your theme variables so you can reference your design tokens in arbitrary values or inline styles." Every utility class resolves to one of those variables: `bg-ink-950` renders as `background-color: var(--color-ink-950)`.

The site's tokens live in `resources/css/app.css`:

```css
@theme {
    --color-ink-950: #0a0d12;
    --color-ink-900: #10151d;
    /* ...the rest of the ink scale... */
}
```

## A theme is a variable remap

Because every class reads a variable at render time, redefining the variables under a selector re-colors everything inside it. Each theme is one block in `resources/css/app.css` keyed on a `data-theme` attribute:

```css
:root[data-theme='ember'] {
    --color-ink-950: #131009;
    --color-emerald-400: #fbbf24;
    --color-cyan-300: #fdba74;
    --grid-line: rgba(74, 59, 40, 0.28);
    --bh-accent: 251, 191, 36;
    --livewire-progress-bar-color: #fbbf24;
}
```

Note the honest trade-off: under `ember`, the class `text-emerald-400` renders **amber**. The templates keep their original color names as *roles* ("the accent color") rather than literal hues. That's the price of zero template churn — documented here so it never surprises anyone reading the Blade files.

## Switching and persisting

The header switcher is a small Alpine component in `resources/views/layouts/app.blade.php` that sets the attribute and remembers the choice:

```js
if (id === 'ink') { delete document.documentElement.dataset.theme; localStorage.removeItem('feras-theme'); }
else { document.documentElement.dataset.theme = id; localStorage.setItem('feras-theme', id); }
```

To avoid a color flash on reload, an inline script in the layout's `<head>` applies the saved theme *before first paint*:

```js
(function () { try { var t = localStorage.getItem('feras-theme'); if (t) { document.documentElement.dataset.theme = t; } } catch (e) {} })();
```

The preference lives in the visitor's own browser — consistent with the site's no-database rule.

## Reaching JavaScript

The black-hole canvas can't use utility classes, so it reads the same variables through the standard CSS API — exactly the pattern the Tailwind docs recommend for JavaScript. From `resources/js/blackhole.js`:

```js
const styles = getComputedStyle(document.documentElement);
const lineColor = styles.getPropertyValue('--grid-line').trim() || 'rgba(42, 54, 70, 0.22)';
const accent = styles.getPropertyValue('--bh-accent').trim() || '52, 211, 153';
```

The switcher re-initializes the effect after a change, so the event horizon's glow follows the theme. `--bh-accent` is stored as a bare `r, g, b` triplet rather than a hex color so the canvas can compose alpha variants (`rgba(${accent}, 0.5)`) without a color parser.

## Further reading

- [Theme variables](https://tailwindcss.com/docs/theme) — `@theme`, the emitted `:root` custom properties, and referencing tokens from JavaScript
