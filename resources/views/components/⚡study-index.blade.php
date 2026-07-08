<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;

new #[Title('Study — Feras')] class extends Component
{
    public string $search = '';

    public string $activeTrack = '';

    public string $activeTag = '';

    public function toggleTrack(string $track): void
    {
        $this->activeTrack = $this->activeTrack === $track ? '' : $track;
    }

    public function toggleTag(string $tag): void
    {
        $this->activeTag = $this->activeTag === $tag ? '' : $tag;
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->activeTrack = '';
        $this->activeTag = '';
    }

    #[Computed]
    public function tracks()
    {
        // Chips in the fixed STUDY_TRACKS order, but only for tracks with entries.
        $present = app(\App\Content\ContentRepository::class)
            ->studyEntries()
            ->pluck('track')
            ->unique();

        return collect(\App\Content\ContentRepository::STUDY_TRACKS)
            ->filter(fn (string $track) => $present->contains($track))
            ->values();
    }

    #[Computed]
    public function allTags()
    {
        return app(\App\Content\ContentRepository::class)
            ->studyEntries()
            ->flatMap(fn (array $entry) => $entry['tags'])
            ->unique()
            ->sort()
            ->values();
    }

    #[Computed]
    public function entries()
    {
        return app(\App\Content\ContentRepository::class)
            ->studyEntries()
            ->filter(function (array $entry) {
                if ($this->search !== ''
                    && stripos($entry['title'], $this->search) === false
                    && stripos($entry['excerpt'], $this->search) === false
                    && stripos($entry['module'] ?? '', $this->search) === false
                    && stripos($entry['track'] ?? '', $this->search) === false) {
                    return false;
                }

                if ($this->activeTrack !== '' && $entry['track'] !== $this->activeTrack) {
                    return false;
                }

                if ($this->activeTag !== '' && ! in_array($this->activeTag, $entry['tags'], true)) {
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
        <x-section-heading prefix="//">study</x-section-heading>
        <p class="text-sm text-ink-400">A public study log — courses, certs, and labs written up in my own words, each with the artifact it produced.</p>
    </header>

    <div class="space-y-4">
        <label class="flex items-center gap-3 rounded-lg border border-ink-800 bg-ink-900 px-4 py-2.5 transition-colors focus-within:border-emerald-400/40 focus-within:ring-2 focus-within:ring-emerald-400/40">
            <span class="font-mono text-sm text-emerald-400" aria-hidden="true">$</span>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="grep study..."
                aria-label="Search study entries"
                class="w-full bg-transparent font-mono text-sm text-ink-100 placeholder-ink-500 focus:outline-none"
            />
        </label>

        <div class="flex flex-wrap items-center gap-2">
            <button
                type="button"
                wire:click="$set('activeTrack', '')"
                @class([
                    'inline-flex items-center rounded border px-2 py-0.5 font-mono text-xs transition-colors',
                    'border-emerald-400/40 bg-emerald-400/10 text-emerald-300' => $activeTrack === '',
                    'border-ink-700/80 bg-ink-900 text-cyan-300 hover:border-ink-600' => $activeTrack !== '',
                ])
            >all</button>

            @foreach ($this->tracks as $track)
                <button
                    type="button"
                    wire:key="track-{{ $track }}"
                    wire:click="toggleTrack('{{ $track }}')"
                    @class([
                        'inline-flex items-center rounded border px-2 py-0.5 font-mono text-xs transition-colors',
                        'border-emerald-400/40 bg-emerald-400/10 text-emerald-300' => $activeTrack === $track,
                        'border-ink-700/80 bg-ink-900 text-cyan-300 hover:border-ink-600' => $activeTrack !== $track,
                    ])
                >--track={{ $track }}</button>
            @endforeach

            @if ($this->allTags->isNotEmpty())
                <span class="text-ink-700" aria-hidden="true">|</span>

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
            @endif
        </div>
    </div>

    {{-- Skeleton cards while a search/filter roundtrip is in flight (.delay avoids flicker on fast responses) --}}
    <div wire:loading.delay class="space-y-4" aria-hidden="true">
        @foreach (range(1, 3) as $i)
            <div class="animate-pulse rounded-lg border border-ink-800 bg-ink-900 p-5">
                <div class="flex gap-6">
                    <div class="h-3 w-20 rounded bg-ink-800"></div>
                    <div class="h-3 w-24 rounded bg-ink-850"></div>
                </div>
                <div class="mt-3 h-5 w-2/3 rounded bg-ink-800"></div>
                <div class="mt-3 h-3 w-full rounded bg-ink-850"></div>
                <div class="mt-4 flex gap-2">
                    <div class="h-5 w-14 rounded bg-ink-850"></div>
                    <div class="h-5 w-14 rounded bg-ink-850"></div>
                </div>
            </div>
        @endforeach
    </div>

    <div wire:loading.remove.delay>
    @if ($this->entries->isEmpty())
        <div class="rounded-lg border border-ink-800 bg-ink-900 p-6 text-center">
            @php
                $activeFilter = $search !== '' ? $search : ($activeTrack !== '' ? '--track='.$activeTrack : ($activeTag !== '' ? $activeTag : 'all'));
            @endphp
            <p class="font-mono text-sm text-ink-400">
                no results for '<span class="text-ink-200">{{ $activeFilter }}</span>'
            </p>
            <button
                type="button"
                wire:click="resetFilters"
                class="mt-4 inline-flex items-center rounded border border-emerald-400/40 bg-emerald-400/10 px-3 py-1 font-mono text-xs text-emerald-300 transition-colors hover:bg-emerald-400/20 focus:outline-none focus:ring-2 focus:ring-emerald-400/40"
            >$ reset --filters</button>
        </div>
    @else
        <ul class="space-y-4">
            @foreach ($this->entries as $entry)
                <li wire:key="entry-{{ $entry['slug'] }}">
                    <a
                        href="{{ route('study.show', $entry['slug']) }}"
                        wire:navigate
                        class="group block rounded-lg border border-ink-800 bg-ink-900 p-5 transition-colors hover:border-ink-700 focus:outline-none focus:ring-2 focus:ring-emerald-400/40"
                    >
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 font-mono text-xs text-ink-500">
                            <time datetime="{{ $entry['date']->format('Y-m-d') }}">{{ $entry['date']->format('Y-m-d') }}</time>
                            @if ($entry['track'])
                                <span aria-hidden="true">·</span>
                                <x-tag>{{ $entry['track'] }}</x-tag>
                            @endif
                            @if ($entry['module'])
                                <span aria-hidden="true">·</span>
                                <span>{{ $entry['module'] }}</span>
                            @endif
                            <span aria-hidden="true">·</span>
                            <span>{{ $entry['readingTime'] }} min read</span>
                        </div>
                        <h3 class="mt-2 font-mono text-lg font-semibold text-ink-100 transition-colors group-hover:text-emerald-300">{{ $entry['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-300">{{ $entry['excerpt'] }}</p>
                        @if (! empty($entry['tags']))
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($entry['tags'] as $tag)
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
</div>
