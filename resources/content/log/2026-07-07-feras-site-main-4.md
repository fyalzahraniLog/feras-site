---
type: activity
title: "Three dark themes: ink, ember, and nebula"
date: "2026-07-07T21:12:30+03:00"
project: feras-site
branch: main
repo: https://github.com/fyalzahraniLog/feras-site
commit: d6bc6e9
slug: 2026-07-07-feras-site-main-4
tags: [feras-site, tailwind, theming]
---

- The site now ships three switchable dark palettes — **ink** (emerald on blue-black, the original), **ember** (amber on warm charcoal), and **nebula** (pink on deep purple) — picked from three colored dots in the header.
- The implementation leans on Tailwind 4's CSS-first design: every utility resolves to a CSS variable, so each theme is one `:root[data-theme=…]` block remapping the ink scale and accent variables. Not a single Blade template changed.
- Your choice persists in your own browser and applies before first paint, so there's no color flash on reload — consistent with the site's no-database rule.
- The black-hole cursor effect is theme-aware too: it reads the grid, background, and accent colors from the active theme's variables and re-initializes on switch — under nebula, the event horizon glows pink.
