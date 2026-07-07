---
type: activity
title: "Feras-Coach walkthroughs, first tests, and push-to-publish"
date: "2026-07-07T20:07:20+03:00"
project: feras-site
branch: main
repo: https://github.com/fyalzahraniLog/feras-site
commit: 9651c81
slug: 2026-07-07-feras-site-main-3
tags: [feras-site, feras-coach, testing]
---

- Shipped **Feras-Coach**: every Laravel and Livewire doc page now has a guided, code-first walkthrough at `/docs/{slug}/coach` — ordered build steps you click through with `$ next`, a terminal progress bar, and completion checkmarks. Stepping is fully client-side (instant), and your progress lives in your own browser, true to the site's no-database design. Nine walkthroughs seeded, authored and maintained by a third content agent.
- The site got its **first real test suite**: 23 Pest tests covering the walkthrough parser (including the fenced-`##` edge case), routes and 404s, and a seed-integrity check that mechanically enforces the authoring contract — every step must open with a code block.
- Publishing became push-to-publish: all secrets moved to Vercel's encrypted environment store, `vercel.json` became secret-free and committed, and the project is GitHub-connected — writing a post is now just commit and push, live in under a minute.
- A privacy pass removed personal data from the homepage and scrubbed it from the repository's entire git history; the contact email moved into the site-wide footer.
