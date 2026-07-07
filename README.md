# feras-site

My personal site — a CV that ships. **Live at [feras-site.vercel.app](https://feras-site.vercel.app).**

Built with **Laravel 13 + Livewire 4 + Tailwind CSS 4**, and deliberately **database-free**: every post and doc page is a Markdown file in this repo, parsed at request time. The whole site deploys as a single PHP serverless function on Vercel.

## The three sections

- **About Me** (`/`) — the CV as a landing page: projects, experience, education, skills — under a cursor-following black hole that bends the grid background.
- **Log Dev** (`/log`) — a GitHub-style contribution feed. An AI agent registers my branch work as activity entries: a `project:branch` badge, a one-line title, and an expandable bullet summary it writes from the commits. Live search, tag filters, and `--since=1d/1w/1m` time windows via Livewire.
- **DOC** (`/docs`) — documentation for my projects, styled like laravel.com/docs: grouped sidebar, "On this page" TOC with scrollspy. Pages are maintained by an agent that fetches the official Laravel/Livewire docs first and only quotes this repo's real code as examples.

## How it works

- **No database.** [`app/Content/ContentRepository.php`](app/Content/ContentRepository.php) parses Markdown + YAML front matter from `resources/content/{log,docs}` per request (memoized singleton). Sessions are cookie-based; cache is in-memory.
- **Livewire single-file components.** Each page is one `⚡*.blade.php` file — anonymous PHP class and Blade template together — in `resources/views/components/`, routed with `Route::livewire()`.
- **The black hole.** The grid background is a `<canvas>` whose lines bend toward a cursor-trailing ball with a softened inverse-square pull, capped below the grid spacing so lines can never cross. Reduced-motion and touch users keep the static CSS grid. See [`resources/js/blackhole.js`](resources/js/blackhole.js).
- **Serverless PHP on Vercel.** [`api/index.php`](api/index.php) + the community `vercel-php` runtime (PHP 8.5); compiled views and framework caches point at `/tmp`. Deployment config (`vercel.json`) is intentionally gitignored — it carries the production `APP_KEY`.

## Run it locally

```bash
composer install
npm install && npm run build
cp .env.example .env && php artisan key:generate
php artisan serve
```

Content is just files — add a post by dropping a `.md` file into `resources/content/log/`. The format contract is documented on the site itself: [/docs/writing-content](https://feras-site.vercel.app/docs/writing-content).

## Quality bar

Meaningful changes ship with Pest tests, `pint --dirty` before commits, and an updated [attacksurface.md](attacksurface.md) when exposure changes — conventions in [CLAUDE.md](CLAUDE.md).

---

**Feras Alzahrani** — [GitHub](https://github.com/fyalzahraniLog) · [LinkedIn](https://www.linkedin.com/in/feras-al-zahrani-04743a2a4) · Fyalzahrani@hotmail.com
