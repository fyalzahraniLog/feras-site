<?php

use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('DOC — Feras')] class extends Component
{
    public string $search = '';

    #[Computed]
    public function groups()
    {
        $search = trim($this->search);

        $repository = app(\App\Content\ContentRepository::class);

        return $repository->groupDocs(
            $repository->docs()->filter(fn (array $doc) => $search === ''
                || stripos($doc['title'], $search) !== false
                || stripos($doc['excerpt'], $search) !== false)
        );
    }
};
?>

<div class="space-y-10">
    <header class="space-y-3">
        <x-section-heading prefix="//">doc</x-section-heading>
        <p class="text-ink-400">Documentation for my projects — how they're built and how to work on them, kept easy to come back to.</p>
    </header>

    <div class="relative">
        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center font-mono text-emerald-400" aria-hidden="true">$</span>
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="search docs..."
            aria-label="Search docs"
            class="w-full rounded-lg border border-ink-800 bg-ink-900 py-2.5 pl-9 pr-4 font-mono text-sm text-ink-100 placeholder:text-ink-500 transition-colors focus:border-ink-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/40"
        />
    </div>

    @if ($this->groups->isEmpty())
        <div class="rounded-lg border border-ink-800 bg-ink-900 p-8 text-center font-mono text-sm">
            <p class="text-ink-300">$ grep -ri "{{ $search }}" ./docs</p>
            <p class="mt-2 text-ink-500">no matches found</p>
            <button
                type="button"
                wire:click="$set('search', '')"
                class="mt-6 inline-flex items-center rounded border border-emerald-400/40 bg-emerald-400/10 px-3 py-1 font-mono text-xs text-emerald-300 transition-colors hover:bg-emerald-400/20 focus:outline-none focus:ring-2 focus:ring-emerald-400/40"
            >
                $ clear search
            </button>
        </div>
    @else
        <div class="space-y-10">
            @foreach ($this->groups as $category => $docs)
                <section class="space-y-4" wire:key="category-{{ Str::slug($category) }}">
                    <h3 class="font-mono text-sm text-ink-500"># {{ $category }}</h3>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @foreach ($docs as $doc)
                            <a
                                href="{{ route('docs.show', $doc['slug']) }}"
                                wire:navigate
                                wire:key="doc-{{ $doc['slug'] }}"
                                class="group flex flex-col rounded-lg border border-ink-800 bg-ink-900 p-5 transition-colors hover:border-ink-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/40"
                            >
                                <h4 class="font-mono font-semibold text-ink-100 transition-colors group-hover:text-emerald-300">
                                    {{ $doc['title'] }}
                                </h4>

                                <p class="mt-2 flex-1 text-sm text-ink-400">{{ $doc['excerpt'] }}</p>

                                <p class="mt-4 font-mono text-xs text-ink-500">
                                    updated {{ $doc['updated']->format('Y-m-d') }}
                                </p>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif
</div>
