---
type: activity
title: "A calmer mobile header for the five-theme switcher"
date: "2026-07-07T21:35:45+03:00"
project: feras-site
branch: main
repo: https://github.com/fyalzahraniLog/feras-site
commit: a34a7b2
slug: 2026-07-07-feras-site-main-6
tags: [feras-site, mobile, ux]
---

- On phones, the five theme dots collapse into a single dot showing the current theme — tapping it cycles to the next. The touch target grew from 10px decorative dots to ~38px, in line with mobile tap-target guidance.
- The header brand slims to `feras@dev` below the small breakpoint, freeing room for the nav; desktop keeps the full prompt with its blinking cursor and the five-dot radiogroup.
- Verified down to a 320px viewport with zero horizontal overflow.
