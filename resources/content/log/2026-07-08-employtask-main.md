---
type: activity
title: "Employ.Task: one-click demo logins so visitors skip registration"
date: "2026-07-08T09:43:27+03:00"
project: employTask
branch: main
repo: https://github.com/fyalzahraniLog/employTask
commit: b2d814b
slug: 2026-07-08-employtask-main
tags: [employtask, laravel, alpine]
---

- Added two demo-account cards to the login page so visitors can try the app without registering — one click fills the email and password fields via Alpine.js, powered by an `x-data` scope with `x-model`-bound inputs.
- Seeded the matching demo users with `firstOrCreate`, so re-running the seeder on the live database is idempotent instead of crashing on duplicate emails.
- Fixed a production seeding crash: the Breeze starter's `User::factory()` call needs Faker, which is a dev-only Composer dependency and absent from `--no-dev` deploys — removed the leftover scaffolding.
- Hardened old-input restore by passing `old('email')` through Blade's `@js()` directive instead of interpolating it into a JavaScript string, and fixed the form labels ("Emile" → "Email").
- Verified end-to-end in a headless browser: card clicks fill the fields, submitting logs in as the demo user, and a failed login repopulates the email with the error message shown.
