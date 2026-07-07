# Attack Surface — feras-site

Last updated: 2026-07-07 (initial entry, at first public deployment)

## feras-site.vercel.app

- **What:** public portfolio/CV site — Laravel 13 + Livewire 4 on Vercel serverless (community `vercel-php@0.9.0` runtime, PHP 8.5). [VERIFIED 2026-07-07]
- **Exposure:** fully public production alias `https://feras-site.vercel.app`. Deployment-specific URLs (`feras-site-*-fyalzahranilogs-projects.vercel.app`) are behind Vercel Authentication (SSO redirect). [VERIFIED 2026-07-07]
- **Authentication:** none — the site has no login, no admin, no user accounts.
- **Data:** none. No database, no user data stored. Content is read-only Markdown bundled with the deployment. Sessions are cookie-driver (encrypted client-side cookies), cache is in-memory array.
- **User input paths:** Livewire component properties only — search strings and filter values on `/log` and `/docs` (compared server-side with `stripos`, echoed back through Blade `{{ }}` escaping), and route slugs (looked up against the content collection; unknown → 404). Livewire payloads are checksum-signed with `APP_KEY`.
- **Rendered HTML risk:** `{!! !!}` is used only for CommonMark output of repo-bundled Markdown files — trusted authorship (me/agents via reviewed commits), not user-submitted. [VERIFIED in x-prose component]
- **Secrets:** `APP_KEY` lives in `vercel.json`, which is **gitignored** — it must never be committed (this repo is public-facing). Location only; value not recorded here. Rotating it invalidates nothing but session cookies.
- **Headers observed:** HSTS (preload) via Vercel; cookies `secure; httponly; samesite=lax`. [VERIFIED 2026-07-07]
- **Deploy path:** local `vercel deploy --prod` via CLI under the fyalzahranilogs-projects team. No git integration yet (repo has no remote).
- **Known accepted risks:** `x-powered-by: PHP/8.5.2` header disclosure (cosmetic); Laravel/Livewire route surface (`/livewire-*/update` POST endpoint — standard framework surface, checksummed).

Re-assess when: a custom domain is added, the repo gets a public remote/CI deploy path, any form or user-persisted input is introduced, or a database appears.
