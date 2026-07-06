---
title: "Deploying Laravel to Vercel (Yes, Really)"
date: 2026-07-03
tags: [deployment, vercel, laravel]
excerpt: "Getting this Laravel + Livewire site running on Vercel's serverless platform — the runtime trick, the gotchas, and the config that finally worked."
---

This site needed a home, and I wanted deploys to be boring: push to `main`, wait thirty seconds, done. Vercel gives me that for free — but it's built for Node and static sites, not PHP. Here's how I got Laravel running on it anyway.

## The serverless PHP runtime

Vercel doesn't ship a PHP runtime, but the community `vercel-php` runtime wraps PHP-FPM in a serverless function. Every request hits a single entrypoint that boots the framework:

```json
{
  "functions": {
    "api/index.php": { "runtime": "vercel-php@0.7.3" }
  },
  "routes": [
    { "src": "/build/(.*)", "dest": "/public/build/$1" },
    { "src": "/(.*)", "dest": "/api/index.php" }
  ]
}
```

The `api/index.php` file is a thin shim that just requires `public/index.php`. Static assets get routed straight to the built files so they never touch PHP at all.

## Gotchas I hit

A serverless filesystem is read-only except for `/tmp`, which breaks a few Laravel defaults:

- **Compiled views** — point `VIEW_COMPILED_PATH` at `/tmp/views` and create the directory at boot
- **Sessions** — switched the driver to `cookie`; there's no disk to persist to
- **Cache** — `array` for now; this site is content-only markdown, so nothing needs to survive a request
- **Logs** — `stderr`, so they show up in Vercel's function logs instead of vanishing into a file that gets thrown away

The markdown content works out fine because it ships inside the deployment bundle — the ContentRepository only ever *reads* from `resources/content`, never writes.

## Was it worth it?

Honestly, yes. Cold starts sit around 300ms, warm requests are far quicker, and the whole pipeline is `git push`. For a personal site rebuilt from flat files on every deploy, that's exactly the amount of infrastructure I want to think about: none.

Next up: making the log searchable without a page reload.
