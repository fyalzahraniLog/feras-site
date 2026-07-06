---
title: Getting Started
category: General
order: 1
excerpt: What the DOC section is for and how it's organized.
---

The **DOC** section collects reference material that outlives a single dev-log post: setup guides, project documentation, and notes I keep coming back to.

## How it's organized

Each page is a Markdown file with front matter:

```yaml
---
title: Getting Started
category: General
order: 1
excerpt: One-line summary shown in lists.
---
```

Pages are grouped by `category` and sorted by `order`. Adding a new page is just dropping a new `.md` file into `resources/content/docs/`.
