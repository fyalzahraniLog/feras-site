---
title: "Hello, World: Why I Built This Site"
date: 2026-07-01
tags: [meta, laravel, livewire]
excerpt: "Kicking off the dev log — what this site is, how it's built, and what I plan to write about here."
---

Welcome to the dev log. This site is my CV turned into a living webpage: **About Me** covers who I am, this **Log Dev** section tracks what I'm building, and **DOC** collects longer-form notes and documentation.

## How it's built

The stack is intentionally simple:

- **Laravel** for routing and views
- **Livewire** for interactive bits like search and filtering
- **Markdown files** instead of a database — every post here is just a `.md` file with front matter
- **Tailwind CSS** for the dark, terminal-flavored design

```bash
$ tree resources/content
resources/content
├── log/     # dev log posts
└── docs/    # documentation pages
```

## What's next

I'll log progress on projects, things I learn, and decisions worth remembering. Short posts, shipped often.
