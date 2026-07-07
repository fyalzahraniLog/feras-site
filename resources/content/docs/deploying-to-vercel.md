---
title: "Deploying to Vercel"
category: site
order: 4
excerpt: "How this Laravel + Livewire site runs as a single serverless function on Vercel — read-only filesystem, /tmp caches, and proxy trust."
updated: "2026-07-07"
---

This site runs on Vercel, which has no native PHP support. The whole Laravel application executes as **one serverless function** under the community [vercel-php](https://github.com/vercel-community/php) runtime (`vercel-php@0.9.0`, PHP 8.5) — a setup that works precisely because the site is database-free. This page documents how the pieces fit, and the three traps that cost a redeploy each.

## The serverless entry point

Laravel's docs are firm that only `public/index.php` should ever face the internet — serving from the project root "will expose many sensitive configuration files to the public Internet." On Vercel, functions live under `api/`, so `api/index.php` simply hands off to the real front controller:

```php
// Vercel serverless entry point — hands every request to Laravel's front controller.
require __DIR__.'/../public/index.php';
```

## Routing: static files first, PHP for the rest

`vercel.json` declares the function and the route table. Static assets resolve from the filesystem; everything else falls through to Laravel:

```json
"routes": [
    { "src": "/", "dest": "/api/index.php" },
    { "src": "/index\\.php", "dest": "/api/index.php" },
    { "src": "/\\.htaccess", "dest": "/api/index.php" },
    { "handle": "filesystem" },
    { "src": "/(.*)", "dest": "/api/index.php" }
]
```

**Trap #1:** the first two guard routes are not decorative. With `outputDirectory: "public"`, the `filesystem` handler treats `public/index.php` as the directory index for `/` — and serves its **raw PHP source**. Routing `/` and `/index.php` to the function *before* the filesystem handler closes that hole.

## A read-only filesystem

Laravel expects to write to `storage/` and `bootstrap/cache/` — but a serverless filesystem is read-only except `/tmp`. Environment variables point every writable path there (`VIEW_COMPILED_PATH=/tmp`, plus the `APP_*_CACHE` set), sessions use the **cookie driver**, and the cache store is in-memory `array`. Nothing this site does needs to persist server-side: content is Markdown bundled read-only with the deployment, parsed per request by `app/Content/ContentRepository.php`.

**Trap #2:** the runtime's PHP version must match the framework's syntax. `vercel-php@0.7.x` ships PHP 8.3, which cannot parse the property-hooks syntax in Laravel 13's Symfony 8 components — the site fatals with a parse error in `Request.php`. `vercel-php@0.9.0` (PHP 8.5) resolves it.

## HTTPS behind the proxy

Vercel terminates TLS before the function sees the request, so Laravel would generate `http://` asset URLs — which browsers block as mixed content. The official fix for TLS-terminating proxies is trusting the `X-Forwarded-*` headers; since a cloud platform's proxy IPs are unknowable, `bootstrap/app.php` trusts them all, exactly as the Laravel docs recommend for cloud load balancers:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->trustProxies(at: '*');
})
```

That was **trap #3**: without it, every stylesheet and script pointed at `http://` and the site would have rendered unstyled.

## Secrets and deploying

The production environment — including `APP_KEY` — lives in **Vercel's encrypted environment-variable store** (project → Settings → Environment Variables), never in the repository. That keeps `vercel.json` free of secrets, so it's committed like any other config file.

Deploys are automatic: the project is connected to the GitHub repository, and every push to `main` builds and ships in under a minute. Publishing a post is just committing a Markdown file and pushing — no deploy commands. (`vercel deploy --prod` still works for manual deploys when needed.)

There is no build server dependency beyond `npm run build` (Vite assets) — no database migrations, no queue workers, nothing to reload.

## Further reading

- [Deployment](https://laravel.com/docs/13.x/deployment) — front-controller rule, directory permissions, `APP_DEBUG=false`
- [Configuring Trusted Proxies](https://laravel.com/docs/13.x/requests#configuring-trusted-proxies) — the `trustProxies(at: '*')` guidance for cloud load balancers
