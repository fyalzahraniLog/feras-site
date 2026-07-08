---
type: activity
title: "RILTstack: demo-account cards and a straight-to-login landing page"
date: "2026-07-08T09:45:06+03:00"
project: laravel-RILTstack
branch: master
repo: https://github.com/fyalzahraniLog/laravel-RILTstack
commit: 4e87a67
slug: 2026-07-08-laravel-riltstack-master
tags: [riltstack, laravel, react, inertia]
---

- Added two clickable demo-account cards above the login form so visitors can try the app without registering. A click fills both fields through React refs — Inertia's `<Form>` uses uncontrolled inputs serialized from the DOM at submit, so no component state was needed (and React 19 accepts `ref` as a plain prop).
- Pointed the root URL straight at the login page with `Route::redirect('/', '/login')`, replacing the default Laravel welcome screen. Logged-in visitors pass through to the dashboard automatically via Fortify's guest middleware.
- Rewrote the database seeder to be idempotent with `updateOrCreate`, so it can run on every production deploy: it self-heals a demo account with a wrong password (the cause of "These credentials do not match our records" on the live site) and sets `email_verified_at` so demo users aren't blocked by the `verified` middleware.
- Guarded the 100 fake seeded tasks behind an empty-table check, so repeated deploys don't multiply demo data.
- Verified end-to-end: production build passes, the seeder ran twice back-to-back without crashing, both demo accounts authenticate with the seeded password, and `GET /` answers with a 302 to `/login` on a live server.
