<?php

namespace App\Content;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;

/**
 * File-based content store: Markdown files with YAML front matter,
 * living in resources/content/{log,docs}. No database required.
 */
class ContentRepository
{
    protected MarkdownConverter $converter;

    /** @var array<string, Collection> parsed files per directory, memoized for the request */
    protected array $parsed = [];

    public function __construct()
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new FrontMatterExtension());

        $this->converter = new MarkdownConverter($environment);
    }

    /**
     * All dev log posts, newest first.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function posts(): Collection
    {
        return $this->parseDirectory(resource_path('content/log'))
            ->map(fn (array $entry) => $this->hydratePost($entry))
            ->sortByDesc('date')
            ->values();
    }

    public function post(string $slug): ?array
    {
        return $this->posts()->firstWhere('slug', $slug);
    }

    /**
     * All doc pages, grouped-friendly: sorted by order, then title.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function docs(): Collection
    {
        return $this->parseDirectory(resource_path('content/docs'))
            ->map(fn (array $entry) => $this->hydrateDoc($entry))
            ->sortBy([['order', 'asc'], ['title', 'asc']])
            ->values();
    }

    public function doc(string $slug): ?array
    {
        return $this->docs()->firstWhere('slug', $slug);
    }

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

    protected function hydratePost(array $entry): array
    {
        $matter = $entry['matter'];
        $slug = $matter['slug'] ?? preg_replace('/^\d{4}-\d{2}-\d{2}-/', '', $entry['filename']);
        $plain = trim(strip_tags($entry['html']));

        return [
            'slug' => $slug,
            'title' => $matter['title'] ?? Str::headline($slug),
            'date' => isset($matter['date']) ? Carbon::parse($matter['date']) : $entry['modified'],
            'tags' => $matter['tags'] ?? [],
            'excerpt' => $matter['excerpt'] ?? Str::limit($plain, 160),
            'readingTime' => max(1, (int) ceil(str_word_count($plain) / 200)),
            'html' => $entry['html'],
        ];
    }

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
}
