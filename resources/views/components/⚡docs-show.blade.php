<?php

use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;

        if (app(\App\Content\ContentRepository::class)->doc($slug) === null) {
            abort(404);
        }
    }

    #[Computed]
    public function doc(): array
    {
        return app(\App\Content\ContentRepository::class)->doc($this->slug);
    }

    #[Computed]
    public function groups()
    {
        return app(\App\Content\ContentRepository::class)->docs()->groupBy('category');
    }

    public function rendering($view): void
    {
        $view->title($this->doc['title'].' — DOC — Feras');
    }
};
?>

<div class="lg:grid lg:grid-cols-[220px_1fr] lg:gap-10">
    <div>
        {{-- Mobile: collapsible docs menu --}}
        <details class="mb-8 rounded-lg border border-ink-800 bg-ink-900 lg:hidden">
            <summary class="cursor-pointer select-none px-4 py-3 font-mono text-sm text-emerald-400">
                $ docs menu
            </summary>
            <nav class="space-y-5 border-t border-ink-800 px-4 py-4" aria-label="Docs">
                @foreach ($this->groups as $category => $docs)
                    <div class="space-y-2" wire:key="m-category-{{ Str::slug($category) }}">
                        <p class="font-mono text-xs text-ink-500"># {{ $category }}</p>
                        <ul class="space-y-1">
                            @foreach ($docs as $doc)
                                <li wire:key="m-doc-{{ $doc['slug'] }}">
                                    <a
                                        href="{{ route('docs.show', $doc['slug']) }}"
                                        wire:navigate
                                        @class([
                                            'block border-l-2 py-1 pl-3 text-sm transition-colors',
                                            'border-emerald-400 bg-emerald-400/10 text-emerald-300' => $doc['slug'] === $slug,
                                            'border-transparent text-ink-400 hover:text-ink-100' => $doc['slug'] !== $slug,
                                        ])
                                    >
                                        {{ $doc['title'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </nav>
        </details>

        {{-- Desktop: sticky sidebar --}}
        <aside class="hidden lg:sticky lg:top-20 lg:block">
            <nav class="space-y-6" aria-label="Docs">
                @foreach ($this->groups as $category => $docs)
                    <div class="space-y-2" wire:key="d-category-{{ Str::slug($category) }}">
                        <p class="font-mono text-xs text-ink-500"># {{ $category }}</p>
                        <ul class="space-y-1">
                            @foreach ($docs as $doc)
                                <li wire:key="d-doc-{{ $doc['slug'] }}">
                                    <a
                                        href="{{ route('docs.show', $doc['slug']) }}"
                                        wire:navigate
                                        @class([
                                            'block border-l-2 py-1 pl-3 text-sm transition-colors',
                                            'border-emerald-400 bg-emerald-400/10 text-emerald-300' => $doc['slug'] === $slug,
                                            'border-transparent text-ink-400 hover:text-ink-100' => $doc['slug'] !== $slug,
                                        ])
                                    >
                                        {{ $doc['title'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </nav>
        </aside>
    </div>

    <div class="min-w-0">
        <header class="space-y-3">
            <p class="font-mono text-sm text-ink-500">
                <a href="{{ route('docs.index') }}" wire:navigate class="transition-colors hover:text-ink-300">doc</a>
                <span class="text-ink-600">/</span>
                <span class="text-cyan-300">{{ Str::slug($this->doc['category']) }}</span>
                <span class="text-ink-600">/</span>
            </p>

            <h1 class="font-mono text-2xl font-semibold text-ink-100 sm:text-3xl">
                {{ $this->doc['title'] }}
            </h1>

            <p class="font-mono text-xs text-ink-500">
                updated {{ $this->doc['updated']->format('Y-m-d') }}
            </p>
        </header>

        <div class="mt-8">
            <x-prose :html="$this->doc['html']" />
        </div>

        <footer class="mt-12 border-t border-ink-800 pt-6">
            <a
                href="{{ route('docs.index') }}"
                wire:navigate
                class="font-mono text-sm text-ink-400 transition-colors hover:text-emerald-300"
            >
                &larr; cd ../doc
            </a>
        </footer>
    </div>
</div>
