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
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
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

    /** Fixed sidebar/index group order; unknown categories sort last. */
    public const CATEGORY_ORDER = ['laravel', 'livewire', 'site'];

    public function __construct(protected ?string $contentPath = null)
    {
        $this->contentPath ??= resource_path('content');

        $environment = new Environment([
            // ids on h2/h3 for the "On this page" TOC — no visible anchor element.
            'heading_permalink' => [
                'insert' => 'none',
                'apply_id_to_heading' => true,
                'id_prefix' => '',
                'min_heading_level' => 2,
                'max_heading_level' => 3,
            ],
        ]);
        $environment->addExtension(new CommonMarkCoreExtension);
        $environment->addExtension(new GithubFlavoredMarkdownExtension);
        $environment->addExtension(new FrontMatterExtension);
        $environment->addExtension(new HeadingPermalinkExtension);

        $this->converter = new MarkdownConverter($environment);
    }

    /**
     * All dev log posts, newest first.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function posts(): Collection
    {
        return $this->parseDirectory($this->contentPath.'/log')
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
        return $this->parseDirectory($this->contentPath.'/docs')
            ->map(fn (array $entry) => $this->hydrateDoc($entry))
            ->sortBy([['order', 'asc'], ['title', 'asc']])
            ->values();
    }

    public function doc(string $slug): ?array
    {
        return $this->docs()->firstWhere('slug', $slug);
    }

    /**
     * All feras-coach walkthroughs, keyed by the doc slug they coach.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function coaches(): Collection
    {
        return $this->parseDirectory($this->contentPath.'/coach')
            ->map(fn (array $entry) => $this->hydrateCoach($entry))
            ->values();
    }

    public function coach(string $slug): ?array
    {
        return $this->coaches()->firstWhere('slug', $slug);
    }

    /** True only for a usable walkthrough: file exists AND has at least one step. */
    public function hasCoach(string $slug): bool
    {
        $coach = $this->coach($slug);

        return $coach !== null && $coach['steps'] !== [];
    }

    /**
     * Group docs by category in the fixed CATEGORY_ORDER.
     * Pass a pre-filtered collection (e.g. search results) or nothing for all docs.
     */
    public function groupDocs(?Collection $docs = null): Collection
    {
        return ($docs ?? $this->docs())
            ->groupBy('category')
            ->sortBy(function (Collection $group, string $category) {
                $index = array_search($category, self::CATEGORY_ORDER, true);

                return $index === false ? PHP_INT_MAX : $index;
            });
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
            'type' => $matter['type'] ?? 'post',
            'project' => $matter['project'] ?? null,
            'branch' => $matter['branch'] ?? null,
            'repo' => $matter['repo'] ?? null,
            'commit' => $matter['commit'] ?? null,
            'title' => $matter['title'] ?? Str::headline($slug),
            'date' => $this->parseDate($matter['date'] ?? null) ?? $entry['modified'],
            'tags' => $matter['tags'] ?? [],
            'excerpt' => $matter['excerpt'] ?? Str::limit($plain, 160),
            'readingTime' => max(1, (int) ceil(str_word_count($plain) / 200)),
            'html' => $entry['html'],
        ];
    }

    /** symfony/yaml coerces unquoted YAML dates to int timestamps — accept both forms. */
    protected function parseDate(mixed $value): ?Carbon
    {
        return match (true) {
            $value === null => null,
            is_int($value) => Carbon::createFromTimestampUTC($value),
            default => Carbon::parse($value),
        };
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
            'updated' => $this->parseDate($matter['updated'] ?? null) ?? $entry['modified'],
            'headings' => $this->extractHeadings($entry['html']),
            'html' => $entry['html'],
        ];
    }

    protected function hydrateCoach(array $entry): array
    {
        $matter = $entry['matter'];
        $slug = $matter['slug'] ?? $entry['filename'];
        [$intro, $steps] = $this->splitSteps($entry['html']);

        return [
            'slug' => $slug,
            'title' => $matter['title'] ?? Str::headline($slug),
            'updated' => $this->parseDate($matter['updated'] ?? null) ?? $entry['modified'],
            'intro' => $intro,
            'steps' => $steps,
        ];
    }

    /**
     * Split rendered HTML into [intro, steps] on <h2> boundaries. A '##' inside
     * a fenced code block is already escaped inside <pre> — never a false split.
     *
     * @return array{0: string, 1: list<array{title: string, html: string}>}
     */
    protected function splitSteps(string $html): array
    {
        $parts = preg_split('/(?=<h2\b)/', $html);
        $intro = trim(array_shift($parts) ?? '');

        $steps = [];
        foreach ($parts as $chunk) {
            if (! preg_match('/^<h2[^>]*>(.*?)<\/h2>/s', $chunk, $m)) {
                continue;
            }
            $steps[] = [
                'title' => trim(html_entity_decode(strip_tags($m[1]))),
                'html' => trim(substr($chunk, strlen($m[0]))),
            ];
        }

        return [$intro, $steps];
    }

    /**
     * @return list<array{level: int, id: string, text: string}> h2/h3 anchors for the "On this page" TOC
     */
    protected function extractHeadings(string $html): array
    {
        preg_match_all('/<h([23])[^>]*\bid="([^"]+)"[^>]*>(.*?)<\/h\1>/s', $html, $matches, PREG_SET_ORDER);

        return array_map(fn (array $m) => [
            'level' => (int) $m[1],
            'id' => $m[2],
            'text' => trim(html_entity_decode(strip_tags($m[3]))),
        ], $matches);
    }
}
