---
type: activity
title: "XSS audit: hardening the Markdown pipeline"
date: "2026-07-08T10:35:00+03:00"
project: feras-site
branch: main
repo: https://github.com/fyalzahraniLog/feras-site
commit: fa033b1
slug: 2026-07-08-feras-site-main
tags: [feras-site, security, testing]
---

- Audited every path where dynamic text reaches the page: Blade `{{ }}` escaping covers the search boxes and route slugs, no `x-html` exists anywhere, and the only unescaped sink is `x-prose` rendering CommonMark output of repo-authored Markdown.
- Found the one loose default: league/commonmark ships with `html_input: allow`, so a raw `<script>` tag in a content file would have passed straight through to production. Content is trusted (repo-authored), but most of it is agent-written now — closed as defense in depth with `html_input: strip` and `allow_unsafe_links: false`.
- Verified the hardening changes nothing visible by rendering all 40+ content files before and after and diffing: byte-identical, since real content keeps its HTML inside escaped code fences.
- Pinned the policy with `ContentSecurityTest` over a hostile fixture — script tags stripped, event-handler attributes gone, `javascript:` links never become anchors (CommonMark leaves them as inert text), and code fences still render escaped. Suite now at 24 tests / 143 assertions.
- Refreshed `attacksurface.md`: recorded the GitHub push-to-deploy path as the site's real control plane, added the coach routes, re-verified live headers (HSTS preload, `secure; httponly` cookies), and set an on-change reassessment cadence.
