---
type: activity
title: "Activity feed, docs overhaul, and a black-hole cursor effect"
date: "2026-07-06T22:07:14+03:00"
project: feras-site
branch: main
commit: a4e0ea2
slug: 2026-07-06-feras-site-main-2
tags: [feras-site, livewire, canvas]
---

- Turned Log Dev into a GitHub-style contribution feed: activity entries carry a `project:branch` badge and a one-line title, expand inline to bullet details, and can be filtered by time window (`--since=1d/1w/1m`) alongside the existing search and tags. A `/log-dev` agent skill registers branch work automatically — it reads the commits since the last entry and writes the summary itself (including this one).
- Rebuilt DOC in the style of laravel.com/docs: a `~/me` sidebar grouping pages under `laravel`, `livewire`, and `site`, an "On this page" table of contents with scrollspy, and eight new pages documenting how this site is built — each grounded in the official docs and using this repo's real code as every example. Its purpose is now stated plainly: project documentation kept easy to come back to.
- Added a cursor-following black-hole effect: the grid background became a canvas whose lines bend toward a trailing ball with a softened inverse-square pull, capped so adjacent lines can never cross. It runs site-wide with a smaller ball on content-heavy pages, while reduced-motion and touch users keep the static grid.
- Tuned the visuals along the way — a roomier 48px grid, a larger event-horizon ball — and installed Pest ahead of the site's first real test suite.
