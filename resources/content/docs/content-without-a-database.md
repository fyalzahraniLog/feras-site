---
title: "Content Without a Database"
category: laravel
order: 3
excerpt: "How this site serves posts and docs from Markdown files with front matter — no database, no migrations, just CommonMark."
updated: "2026-07-06"
---

This site has no database. No migrations, no models, no connection to configure or back up. Every dev-log post and doc page — including the one you are reading — is a Markdown file on disk. For a personal site where content changes by editing files in a git repository, a database adds operational weight without adding capability: version control already provides history, review, and rollback, and deploys stay a pure file sync.

The entire content layer is one class: `app/Content/ContentRepository.php`.

## The CommonMark pipeline

The repository builds a `league/commonmark` converter in its constructor, stacking three extensions: the CommonMark core, GitHub-flavored Markdown (tables, task lists, strikethrough), and front matter parsing. From `app/Content/ContentRepository.php`:

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

The `FrontMatterExtension` is the key piece: it strips the YAML block from the top of each file and returns it as structured data alongside the rendered HTML. A doc page like `resources/content/docs/getting-started.md` starts with exactly such a block:

```yaml
---
title: Getting Started
category: General
order: 1
excerpt: What the DOC section is for and how it's organized.
---
```

The repository's `parseDirectory()` method reads every `.md` file in a directory, converts it, and — when the result carries front matter — extracts it:

```php
$result = $this->converter->convert($file->getContents());

$matter = $result instanceof RenderedContentWithFrontMatter
    ? $result->getFrontMatter()
    : [];
```

Results are memoized per request in the `$parsed` array, so listing pages and detail pages share one parse.

## Hydrating files into arrays

Raw front matter is untrusted input — a file might omit its title or date. The `hydratePost()` and `hydrateDoc()` methods turn each parsed file into a predictable array, filling gaps with sensible fallbacks. Here is `hydrateDoc()` from `app/Content/ContentRepository.php`:

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

Every fallback is derived from what the file already provides: the slug comes from the filename, the title is the slug run through `Str::headline()`, the excerpt is trimmed from the rendered body, and `updated` falls back to the file's modification time. `hydratePost()` does the same for log entries, adding post-specific fields like `tags`, `readingTime`, and the git `branch` metadata. The public API is deliberately Eloquent-shaped — `docs()` returns a sorted Collection, `doc($slug)` returns one page or `null` — so consuming code reads exactly as it would with a database.

## Singleton access with app()

Components resolve the repository straight out of the service container with the `app()` helper. The log post component in `resources/views/components/⚡log-show.blade.php` does this in both `mount()` and its computed property:

```php
#[Computed]
public function post()
{
    return app(\App\Content\ContentRepository::class)->post($this->slug);
}
```

The repository is bound as a singleton, so every `app(ContentRepository::class)` call in a request returns the *same* instance — which is what makes the `$parsed` memoization effective. The docs index, the doc page, and the log pages all hit one shared cache of parsed files, and the filesystem is read at most once per directory per request.

## Further reading

- [Service Container](https://laravel.com/docs/13.x/container) — resolving singletons with `app()`
- [Collections](https://laravel.com/docs/13.x/collections) — the fluent API `posts()` and `docs()` return
- [File Storage](https://laravel.com/docs/13.x/filesystem) — working with files in Laravel
