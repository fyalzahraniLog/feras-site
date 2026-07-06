<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;

new #[Title('Log Dev — Feras')] class extends Component
{
    public string $search = '';

    public string $activeTag = '';

    public function toggleTag(string $tag): void
    {
        $this->activeTag = $this->activeTag === $tag ? '' : $tag;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->activeTag = '';
    }

    #[Computed]
    public function allTags()
    {
        return app(\App\Content\ContentRepository::class)
            ->posts()
            ->flatMap(fn (array $post) => $post['tags'])
            ->unique()
            ->sort()
            ->values();
    }

    #[Computed]
    public function posts()
    {
        return app(\App\Content\ContentRepository::class)
            ->posts()
            ->filter(function (array $post) {
                if ($this->search !== ''
                    && stripos($post['title'], $this->search) === false
                    && stripos($post['excerpt'], $this->search) === false) {
                    return false;
                }

                if ($this->activeTag !== '' && ! in_array($this->activeTag, $post['tags'], true)) {
                    return false;
                }

                return true;
            })
            ->values();
    }
};
?>

<div class="space-y-8">
    <header class="space-y-2">
        <x-section-heading prefix="//">log-dev</x-section-heading>
        <p class="text-sm text-ink-400">Short, frequent notes on what I'm building and what I learned along the way.</p>
    </header>

    <div class="space-y-4">
        <label class="flex items-center gap-3 rounded-lg border border-ink-800 bg-ink-900 px-4 py-2.5 transition-colors focus-within:border-emerald-400/40 focus-within:ring-2 focus-within:ring-emerald-400/40">
            <span class="font-mono text-sm text-emerald-400" aria-hidden="true">$</span>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="grep posts..."
                aria-label="Search posts"
                class="w-full bg-transparent font-mono text-sm text-ink-100 placeholder-ink-500 focus:outline-none"
            />
        </label>

        <div class="flex flex-wrap items-center gap-2">
            <button
                type="button"
                wire:click="$set('activeTag', '')"
                @class([
                    'inline-flex items-center rounded border px-2 py-0.5 font-mono text-xs transition-colors',
                    'border-emerald-400/40 bg-emerald-400/10 text-emerald-300' => $activeTag === '',
                    'border-ink-700/80 bg-ink-900 text-cyan-300 hover:border-ink-600' => $activeTag !== '',
                ])
            >all</button>

            @foreach ($this->allTags as $tag)
                <button
                    type="button"
                    wire:key="tag-{{ $tag }}"
                    wire:click="toggleTag('{{ $tag }}')"
                    @class([
                        'inline-flex items-center rounded border px-2 py-0.5 font-mono text-xs transition-colors',
                        'border-emerald-400/40 bg-emerald-400/10 text-emerald-300' => $activeTag === $tag,
                        'border-ink-700/80 bg-ink-900 text-cyan-300 hover:border-ink-600' => $activeTag !== $tag,
                    ])
                >{{ $tag }}</button>
            @endforeach
        </div>
    </div>

    @if ($this->posts->isEmpty())
        <div class="rounded-lg border border-ink-800 bg-ink-900 p-6 text-center">
            <p class="font-mono text-sm text-ink-400">
                no results for '<span class="text-ink-200">{{ $search !== '' ? $search : $activeTag }}</span>'
            </p>
            <button
                type="button"
                wire:click="resetFilters"
                class="mt-4 inline-flex items-center rounded border border-emerald-400/40 bg-emerald-400/10 px-3 py-1 font-mono text-xs text-emerald-300 transition-colors hover:bg-emerald-400/20 focus:outline-none focus:ring-2 focus:ring-emerald-400/40"
            >$ reset --filters</button>
        </div>
    @else
        <ul class="space-y-4">
            @foreach ($this->posts as $post)
                <li wire:key="post-{{ $post['slug'] }}">
                    <a
                        href="{{ route('log.show', $post['slug']) }}"
                        wire:navigate
                        class="group block rounded-lg border border-ink-800 bg-ink-900 p-5 transition-colors hover:border-ink-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/40"
                    >
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 font-mono text-xs text-ink-500">
                            <time datetime="{{ $post['date']->format('Y-m-d') }}">{{ $post['date']->format('Y-m-d') }}</time>
                            <span aria-hidden="true">·</span>
                            <span>{{ $post['readingTime'] }} min read</span>
                        </div>
                        <h3 class="mt-2 font-mono text-lg font-semibold text-ink-100 transition-colors group-hover:text-emerald-300">{{ $post['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-300">{{ $post['excerpt'] }}</p>
                        @if (! empty($post['tags']))
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($post['tags'] as $tag)
                                    <x-tag>{{ $tag }}</x-tag>
                                @endforeach
                            </div>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
