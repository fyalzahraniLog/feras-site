# Attack Surface — feras-site

Last updated: 2026-07-08

| Surface | Exposure | Auth | Data at risk | Last assessed |
| --- | --- | --- | --- | --- |
| feras-site.vercel.app | public | none (no accounts) | none stored | 2026-07-08 |
| github.com/fyalzahraniLog/feras-site | public repo, push-to-deploy | GitHub account | site integrity | 2026-07-08 |

## feras-site.vercel.app

- **What:** public portfolio/CV site — Laravel 13 + Livewire 4 on Vercel serverless (community `vercel-php@0.9.0` runtime, PHP 8.5). [VERIFIED 2026-07-08]
- **Exposure:** fully public production alias `https://feras-site.vercel.app`. Deployment-specific URLs (`feras-site-*-fyalzahranilogs-projects.vercel.app`) are behind Vercel Authentication (SSO redirect). [VERIFIED 2026-07-07]
- **Routes:** all public GET — `/`, `/log`, `/log/{slug}`, `/docs`, `/docs/{slug}`, `/docs/{slug}/coach`, plus Livewire's `/livewire-*/update` POST (checksum-signed with `APP_KEY`). Unknown slugs 404. [VERIFIED 2026-07-08]
- **Authentication:** none — no login, no admin, no user accounts.
- **Data:** none. No database, no user data stored. Content is read-only Markdown bundled with the deployment. Sessions are cookie-driver (encrypted client-side cookies), cache is in-memory array.
- **User input paths:** Livewire component properties only — search strings and filter values on `/log` and `/docs` (compared server-side with `stripos`, echoed back through Blade `{{ }}` escaping), and route slugs (looked up against the content collection; unknown → 404). Client side: no `x-html` anywhere; `?step=` on coach pages is parsed as an int and clamped in Alpine. [VERIFIED 2026-07-08]
- **Rendered HTML risk (XSS):** `{!! !!}` is used only in `x-prose` for CommonMark output of repo-bundled Markdown. Hardened 2026-07-08: `html_input => 'strip'` and `allow_unsafe_links => false` in `ContentRepository`, so raw HTML and `javascript:`/`data:` links in Markdown never reach the sink even if an agent or contributor writes them. Policy pinned by `tests/Feature/ContentSecurityTest.php`; render-diff over all existing content confirmed zero visible change. [VERIFIED 2026-07-08]
- **Secrets:** `APP_KEY` lives in Vercel's encrypted environment-variable store (Production env). [VERIFIED 2026-07-07] `vercel.json` carries no secrets and is committed. Location only; value not recorded here. Rotating it invalidates nothing but session cookies and in-flight Livewire checksums.
- **Headers observed:** HSTS (`max-age=63072000; includeSubDomains; preload`) via Vercel; session cookies `secure; httponly; samesite=lax`; XSRF cookie `secure; samesite=lax`. [VERIFIED 2026-07-08]
- **Known accepted risks:** `x-powered-by: PHP/8.5.2` header disclosure (cosmetic — the repo is public anyway); standard Laravel/Livewire framework route surface; no Content-Security-Policy header (low value while all JS is first-party Vite bundles; revisit if third-party scripts beyond Vercel Insights are added). [CONFIRM: whether vercel.json headers config should add CSP later]

## github.com/fyalzahraniLog/feras-site (deploy path)

- **What:** public GitHub repo, Vercel GitHub integration — **every push to `main` auto-deploys to production**. [VERIFIED 2026-07-08 — `git remote -v`; integration confirmed working 2026-07-07]
- **Exposure:** repo contents are public by design (portfolio). Git history was rewritten 2026-07-07 (filter-repo + force-push) to remove a personal phone number before publication; do not reintroduce it.
- **Risk concentration:** the GitHub account is now the site's real control plane — whoever can push, deploys. Protections: [CONFIRM: MFA enabled on the GitHub account? branch protection is not configured — single-maintainer repo, accepted]
- **Content agents:** `/log-dev`, `/doc-dev`, `/feras-coach` write Markdown that ships to production on push. The 2026-07-08 CommonMark hardening bounds the blast radius of a bad agent write to text content (no script/HTML injection possible). PcHome material must never appear here (standing rule).
- **No CI gate yet:** tests run locally (Pest, 24 tests) but nothing blocks a push that skips them. Planned hardening: GitHub Actions test gate before deploy. [CONFIRM: still pending as of 2026-07-08]

## Assessment cadence

Low-criticality surface (no stored data, no auth, static content): **on-change reassessment** is the primary mechanism — re-assess when any trigger below fires. Otherwise a light quarterly pass (headers, dependency majors, Vercel runtime version) is enough.

Re-assess when: a custom domain is added; any form or user-persisted input is introduced; a database appears; a third-party script is added; CI/deploy topology changes; a new content agent gets write access.

## Changelog

- 2026-07-08 — XSS assessment of the whole render path (Blade sinks, Alpine bindings, Markdown pipeline). Finding: CommonMark defaults (`html_input: allow`, `allow_unsafe_links: true`) left the `{!! !!}` sink one bad agent-write away from stored XSS. Fixed same day (`strip` + unsafe-links off, Pest-pinned). Entry updated: GitHub auto-deploy path recorded (stale "no remote" claim removed), coach routes added, headers re-verified.
- 2026-07-07 — initial entry at first public deployment.
