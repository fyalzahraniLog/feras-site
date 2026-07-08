<?php

use Livewire\Component;
use Livewire\Attributes\Computed;

new class extends Component
{
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;

        if (app(\App\Content\ContentRepository::class)->studyEntry($slug) === null) {
            abort(404);
        }
    }

    public function rendering($view): void
    {
        $view->title($this->entry['title'].' — Study — Feras');
    }

    #[Computed]
    public function entry()
    {
        return app(\App\Content\ContentRepository::class)->studyEntry($this->slug);
    }

    #[Computed]
    public function adjacent()
    {
        $entries = app(\App\Content\ContentRepository::class)->studyEntries();
        $index = $entries->search(fn (array $entry) => $entry['slug'] === $this->slug);

        if ($index === false) {
            return ['older' => null, 'newer' => null];
        }

        // studyEntries() is newest first: next index = older, previous index = newer.
        return [
            'older' => $entries->get($index + 1),
            'newer' => $index > 0 ? $entries->get($index - 1) : null,
        ];
    }
};
?>

<article class="space-y-8">
    <a
        href="{{ route('study.index') }}"
        wire:navigate
        class="inline-flex items-center gap-2 font-mono text-sm text-ink-400 transition-colors hover:text-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-400/40"
    >&larr; cd ../study</a>

    <header class="space-y-4">
        <h1 class="font-mono text-2xl font-semibold text-ink-100 sm:text-3xl">{{ $this->entry['title'] }}</h1>
        <div class="flex flex-wrap items-center gap-x-3 gap-y-2 font-mono text-xs text-ink-500">
            <time datetime="{{ $this->entry['date']->format('Y-m-d') }}">{{ $this->entry['date']->format('Y-m-d') }}</time>
            <span aria-hidden="true">·</span>
            <span>{{ $this->entry['readingTime'] }} min read</span>
            @if ($this->entry['track'])
                <span aria-hidden="true">·</span>
                <x-tag>{{ $this->entry['track'] }}</x-tag>
            @endif
            @if ($this->entry['module'])
                <span aria-hidden="true">·</span>
                <span>{{ $this->entry['module'] }}</span>
            @endif
            @if (! empty($this->entry['tags']))
                <span aria-hidden="true">·</span>
                <span class="flex flex-wrap gap-2">
                    @foreach ($this->entry['tags'] as $tag)
                        <x-tag>{{ $tag }}</x-tag>
                    @endforeach
                </span>
            @endif
        </div>
        @if ($this->entry['artifact'])
            <a
                href="{{ $this->entry['artifact'] }}"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center gap-2 rounded border border-ink-700/80 bg-ink-900 px-3 py-1.5 font-mono text-xs text-cyan-300 transition-colors hover:border-ink-600 hover:text-cyan-200 focus:outline-none focus:ring-2 focus:ring-emerald-400/40"
            >
                <svg viewBox="0 0 16 16" class="h-3.5 w-3.5 shrink-0" fill="currentColor" aria-hidden="true"><path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27s1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8Z"/></svg>
                <span>view artifact &nearr;</span>
            </a>
        @endif
    </header>

    <x-prose :html="$this->entry['html']" />

    @if ($this->adjacent['older'] || $this->adjacent['newer'])
        <nav class="grid grid-cols-1 gap-4 border-t border-ink-800 pt-8 sm:grid-cols-2" aria-label="Entry navigation">
            @if ($this->adjacent['older'])
                <a
                    href="{{ route('study.show', $this->adjacent['older']['slug']) }}"
                    wire:navigate
                    class="group rounded-lg border border-ink-800 bg-ink-900 p-5 transition-colors hover:border-ink-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/40"
                >
                    <span class="font-mono text-xs text-ink-500">&larr; older</span>
                    <span class="mt-1 block font-mono text-sm text-ink-200 transition-colors group-hover:text-emerald-300">{{ $this->adjacent['older']['title'] }}</span>
                </a>
            @else
                <span aria-hidden="true"></span>
            @endif

            @if ($this->adjacent['newer'])
                <a
                    href="{{ route('study.show', $this->adjacent['newer']['slug']) }}"
                    wire:navigate
                    class="group rounded-lg border border-ink-800 bg-ink-900 p-5 text-right transition-colors hover:border-ink-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/40 sm:col-start-2"
                >
                    <span class="font-mono text-xs text-ink-500">newer &rarr;</span>
                    <span class="mt-1 block font-mono text-sm text-ink-200 transition-colors group-hover:text-emerald-300">{{ $this->adjacent['newer']['title'] }}</span>
                </a>
            @endif
        </nav>
    @endif
</article>
