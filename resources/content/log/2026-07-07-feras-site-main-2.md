---
type: activity
title: "Analytics, loading skeletons, and two new doc pages"
date: "2026-07-07T10:30:35+03:00"
project: feras-site
branch: main
repo: https://github.com/fyalzahraniLog/feras-site
commit: 23123c8
slug: 2026-07-07-feras-site-main-2
tags: [feras-site, livewire, analytics]
---

- Turned on Vercel Web Analytics: a cookieless beacon script loads in production only (behind Blade's `@production` directive), tracking pageviews — including `wire:navigate` transitions, which count via the History API.
- Added loading states for the site's server roundtrips: skeleton cards mirror the real post and doc cards while a search or filter request is in flight, gated by `wire:loading.delay` so they only appear when a response takes over 200ms — built for serverless cold starts, invisible on fast responses.
- Themed the `wire:navigate` progress bar emerald through Livewire's config rather than CSS — the package injects its styles after the app stylesheet, so the supported `progress_bar_color` option is the fix that actually wins.
- The DOC section grew two reference pages: [Deploying to Vercel](/docs/deploying-to-vercel), covering the serverless setup and its three traps, and [Loading States & Skeletons](/docs/loading-states), covering the patterns above.
