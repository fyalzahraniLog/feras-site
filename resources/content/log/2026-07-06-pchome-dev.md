---
type: activity
title: "Deck management, guided tours, and a security audit for PcHome"
date: "2026-07-06T14:12:30+03:00"
project: PcHome
branch: dev
repo: https://github.com/mhusef/PcHome
commit: bfd06d9
slug: 2026-07-06-pchome-dev
tags: [pchome, react, security]
---

- Shipped the HigherOrLower deck management system: a deck detail modal, publishing controls with validation rules, status badges, and match-setup integration — backed by new feature tests for deck publishing and featured decks.
- Added contextual product tours and "How to Play" modals built on driver.js: a `useCoachTour` hook, tour persistence in local storage, and custom tour styling across the game and match pages.
- Completed a backend security audit with follow-up documentation: a full audit report, a sequenced remediation plan covering 77 findings with acceptance criteria and guardrails, and an attack-surface inventory.
- Merged three pull requests on `dev` (#21, #22, #24), moving both the game UX and the project's security posture forward.
