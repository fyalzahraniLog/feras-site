<?php

use Livewire\Component;
use Livewire\Attributes\Computed;

new class extends Component
{
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;

        if (app(\App\Content\ContentRepository::class)->post($slug) === null) {
            abort(404);
        }
    }

    public function rendering($view): void
    {
        $view->title($this->post['title'].' — Log Dev — Feras');
    }

    #[Computed]
    public function post()
    {
        return app(\App\Content\ContentRepository::class)->post($this->slug);
    }

    #[Computed]
    public function adjacent()
    {
        $posts = app(\App\Content\ContentRepository::class)->posts();
        $index = $posts->search(fn (array $post) => $post['slug'] === $this->slug);

        if ($index === false) {
            return ['older' => null, 'newer' => null];
        }

        // posts() is newest first: next index = older, previous index = newer.
        return [
            'older' => $posts->get($index + 1),
            'newer' => $index > 0 ? $posts->get($index - 1) : null,
        ];
    }
};
?>

<article class="space-y-8">
    <a
        href="{{ route('log.index') }}"
        wire:navigate
        class="inline-flex items-center gap-2 font-mono text-sm text-ink-400 transition-colors hover:text-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-400/40"
    >&larr; cd ../log-dev</a>

    <header class="space-y-4">
        <h1 class="font-mono text-2xl font-semibold text-ink-100 sm:text-3xl">{{ $this->post['title'] }}</h1>
        <div class="flex flex-wrap items-center gap-x-3 gap-y-2 font-mono text-xs text-ink-500">
            <time datetime="{{ $this->post['date']->format('Y-m-d') }}">{{ $this->post['date']->format('Y-m-d') }}</time>
            <span aria-hidden="true">·</span>
            <span>{{ $this->post['readingTime'] }} min read</span>
            @if (! empty($this->post['tags']))
                <span aria-hidden="true">·</span>
                <span class="flex flex-wrap gap-2">
                    @foreach ($this->post['tags'] as $tag)
                        <x-tag>{{ $tag }}</x-tag>
                    @endforeach
                </span>
            @endif
        </div>
    </header>

    <x-prose :html="$this->post['html']" />

    @if ($this->adjacent['older'] || $this->adjacent['newer'])
        <nav class="grid grid-cols-1 gap-4 border-t border-ink-800 pt-8 sm:grid-cols-2" aria-label="Post navigation">
            @if ($this->adjacent['older'])
                <a
                    href="{{ route('log.show', $this->adjacent['older']['slug']) }}"
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
                    href="{{ route('log.show', $this->adjacent['newer']['slug']) }}"
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
