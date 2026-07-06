---
type: activity
title: "CV landing site built, styled, and filled with real data"
date: "2026-07-06T14:11:19+03:00"
project: feras-site
branch: main
commit: 0c59420
slug: 2026-07-06-feras-site-main
tags: [feras-site, laravel, livewire]
---

- Scaffolded a fresh Laravel 13 app with Livewire 4 and Tailwind 4, plus a database-free content system: Markdown files with YAML front matter parsed by a memoized `ContentRepository` singleton.
- Built the three site sections in a dark terminal theme — a landing page with a fake-terminal hero, About Me, skills, and experience timeline; a Log Dev feed with live search and tag filters; and a DOC section with categorized index and sticky sidebar.
- Replaced every placeholder with real CV data extracted from the attached PDF: six project cards with working GitHub, live-demo, and Figma links, Harf IT experience, Umm Al-Qura education, Udemy certifications, and full skill groups.
- Hardened the build after review: unified card styling and hover affordances, added accessibility labels, and made all whole-card links clickable.
