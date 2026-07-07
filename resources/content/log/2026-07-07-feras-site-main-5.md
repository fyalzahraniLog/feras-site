---
type: activity
title: "Five themes now, and the theming system documented"
date: "2026-07-07T21:27:58+03:00"
project: feras-site
branch: main
repo: https://github.com/fyalzahraniLog/feras-site
commit: a98648e
slug: 2026-07-07-feras-site-main-5
tags: [feras-site, tailwind, theming]
---

- The theme set grew from three to five: **ocean** (sky blue on deep navy) and **crimson** (red on dark maroon) join ink, ember, and nebula. Each new theme cost ~20 lines of CSS variables and one line in the switcher — the payoff of the variable-remap design.
- The DOC section gained [Theming with CSS Variables](/docs/theming): how Tailwind 4's utilities-are-variables architecture makes a whole theme one `data-theme` block, the pre-paint persistence trick, and how the black-hole canvas reads the same variables from JavaScript.
- The header dots shrink slightly on small screens so all five fit comfortably next to the nav.
