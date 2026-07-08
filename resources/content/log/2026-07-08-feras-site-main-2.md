---
type: activity
title: "New /study section: a public study log with its own agent"
date: "2026-07-08T12:45:00+03:00"
project: feras-site
branch: main
repo: https://github.com/fyalzahraniLog/feras-site
commit: afb3c48
slug: 2026-07-08-feras-site-main-2
tags: [feras-site, livewire, testing]
---

- Added a fourth site section: `/study`, a chronological study log for courses, certs, and labs — each entry written first-person and linking the GitHub artifact it produced.
- New study content type in `ContentRepository`: `track`/`module`/`artifact` front matter, a `STUDY_TRACKS` whitelist, and the same date-coercion and excerpt fallbacks the dev log uses.
- Built the feed and entry pages as Livewire single-file components: `$ grep study...` live search, `--track=` and tag filter chips, skeleton loaders, older/newer navigation, and an artifact button on entries that ship one.
- Documented the entry format as a parser contract in the site's writing-content doc, so the new `/study-log` agent (the site's fourth content agent) writes against a spec instead of guessing.
- 13 new Pest tests bring the suite to 37 — including seed-integrity tests that mechanically reject any future entry with an unknown track or a non-https artifact link.
- Registered the new routes and content agent in the attack-surface inventory; seeded the section with a first entry on the study plan itself.
