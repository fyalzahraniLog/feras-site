---
type: activity
title: "The site is live: serverless Laravel on Vercel"
date: "2026-07-07T08:16:58+03:00"
project: feras-site
branch: main
repo: https://github.com/fyalzahraniLog/feras-site
commit: 87785f3
slug: 2026-07-07-feras-site-main
tags: [feras-site, vercel, deployment]
---

- Deployed the site to production: it now runs at [feras-site.vercel.app](https://feras-site.vercel.app) as a single PHP 8.5 serverless function on the community `vercel-php` runtime — viable because the site is database-free, with cookie sessions and framework caches redirected to `/tmp` for the read-only filesystem.
- Hardened the deployment along the way: guard routes so Vercel's filesystem handler can never serve `index.php` as raw source, trusted the platform's proxy headers so every generated URL is `https`, and kept the production `APP_KEY` out of git entirely (`vercel.json` is ignored).
- Wrote the site's first `attacksurface.md` entry at the moment it went public — no auth, no database, no stored user input; the exposure inventory now ships with the repo.
- Published the repository to GitHub with a proper portfolio README, and made `writing-content.md` the single source of truth for the content-format contracts that the site's agents follow.
