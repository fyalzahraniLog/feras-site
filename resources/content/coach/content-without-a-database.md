---
title: "Content Without a Database"
updated: "2026-07-07"
---

By the end of this walkthrough you'll have rebuilt this site's entire content layer from scratch: one class that turns a directory of Markdown files into queryable Collections — no database, no migrations. Every step adds one real piece of `app/Content/ContentRepository.php`.

## Create the content directory and a first file

```yaml
---
title: Getting Started
category: site
order: 1
excerpt: What the DOC section is for and how it's organized.
---
```

Make a `resources/content/docs/` directory and save this as `getting-started.md` — the YAML block between the `---` fences is the front matter, and everything below it is plain Markdown body. That's the whole storage engine: files on disk, versioned by git, so history, review, and rollback come for free and deploys stay a pure file sync.

## Set up the CommonMark environment

```php
public function __construct()
{
    $environment = new Environment();
    $environment->addExtension(new CommonMarkCoreExtension());
    $environment->addExtension(new GithubFlavoredMarkdownExtension());
    $environment->addExtension(new FrontMatterExtension());

    $this->converter = new MarkdownConverter($environment);
}
```

Create `app/Content/ContentRepository.php` and build a `league/commonmark` converter in its constructor, stacking three extensions: the CommonMark core, GitHub-flavored Markdown (tables, task lists, strikethrough), and front matter parsing. The `FrontMatterExtension` is the key piece — it strips the YAML block you wrote in the last step and hands it back as structured data alongside the rendered HTML.

## Parse a directory of Markdown files

```php
protected function parseDirectory(string $path): Collection
{
    return collect(File::files($path))
        ->filter(fn ($file) => $file->getExtension() === 'md')
        ->map(function ($file) {
            $result = $this->converter->convert($file->getContents());

            $matter = $result instanceof RenderedContentWithFrontMatter
                ? $result->getFrontMatter()
                : [];

            return [
                'filename' => $file->getFilenameWithoutExtension(),
                'modified' => Carbon::createFromTimestamp($file->getMTime()),
                'matter' => $matter,
                'html' => $result->getContent(),
            ];
        });
}
```

This reads every `.md` file in a directory, converts it, and — when the result carries front matter — extracts it via the `instanceof RenderedContentWithFrontMatter` check; a file with no YAML block just gets an empty array. Notice you also keep the filename and the file's modification time: both become fallbacks in the next step.

## Hydrate entries into predictable arrays

```php
protected function hydrateDoc(array $entry): array
{
    $matter = $entry['matter'];
    $slug = $matter['slug'] ?? $entry['filename'];
    $plain = trim(strip_tags($entry['html']));

    return [
        'slug' => $slug,
        'title' => $matter['title'] ?? Str::headline($slug),
        'category' => $matter['category'] ?? 'General',
        'order' => $matter['order'] ?? 999,
        'excerpt' => $matter['excerpt'] ?? Str::limit($plain, 160),
        'updated' => isset($matter['updated']) ? Carbon::parse($matter['updated']) : $entry['modified'],
        'html' => $entry['html'],
    ];
}
```

Raw front matter is untrusted input — a file might omit its title or date — so every entry passes through a hydrate method that fills gaps with fallbacks derived from what the file already provides: the slug comes from the filename, the title is the slug run through `Str::headline()`, the excerpt is trimmed from the rendered body, and `updated` falls back to the file's modification time. Whatever the author forgot, consumers always get the same predictable array. A sibling `hydratePost()` does the same for log entries, adding post-specific fields like `tags`, `readingTime`, and the git `branch` metadata.

## Expose docs() and doc() lookups

```php
public function docs(): Collection
{
    return $this->parseDirectory($this->contentPath.'/docs')
        ->map(fn (array $entry) => $this->hydrateDoc($entry))
        ->sortBy([['order', 'asc'], ['title', 'asc']])
        ->values();
}

public function doc(string $slug): ?array
{
    return $this->docs()->firstWhere('slug', $slug);
}
```

Now wire the pieces together: point `parseDirectory()` at `resources/content/docs`, hydrate every entry, and sort by `order` then `title`. The API is deliberately Eloquent-shaped — `docs()` returns a sorted Collection, `doc($slug)` returns one page or `null` — so consuming code reads exactly as it would with a database. `firstWhere('slug', ...)` works because hydration just guaranteed every entry has a slug. `posts()` and `post($slug)` follow the same pattern over `resources/content/log`.

## Memoize parses per request

```php
/** @var array<string, Collection> parsed files per directory, memoized for the request */
protected array $parsed = [];

protected function parseDirectory(string $path): Collection
{
    if (isset($this->parsed[$path])) {
        return $this->parsed[$path];
    }

    if (! File::isDirectory($path)) {
        return $this->parsed[$path] = collect();
    }

    return $this->parsed[$path] = collect(File::files($path))
        ->filter(fn ($file) => $file->getExtension() === 'md')
        ->map(function ($file) {
            // ... same mapping as the parsing step
        });
}
```

As written, `doc($slug)` re-reads the whole directory every call, so add a `$parsed` array keyed by path: the first call does the work and stores the result, every later call in the same request returns the cached Collection. This means listing pages and detail pages share one parse. It only pays off, though, if everyone talks to the *same* repository instance — that's the final step.

## Bind a singleton and resolve it with app()

```php
// app/Providers/AppServiceProvider.php
public function register(): void
{
    $this->app->singleton(ContentRepository::class);
}
```

Register the repository as a singleton so every `app(ContentRepository::class)` call in a request returns the same instance — which is exactly what makes the `$parsed` memoization effective. Components then resolve it straight out of the service container; the log post component in `resources/views/components/⚡log-show.blade.php` does this in a computed property:

```php
#[Computed]
public function post()
{
    return app(\App\Content\ContentRepository::class)->post($this->slug);
}
```

The docs index, the doc page, and the log pages all hit one shared cache of parsed files, and the filesystem is read at most once per directory per request. You now have the site's entire content layer — and not a single migration.
