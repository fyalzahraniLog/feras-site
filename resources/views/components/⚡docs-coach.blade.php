<?php

use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;

        $repository = app(\App\Content\ContentRepository::class);

        if (! $repository->hasCoach($slug) || $repository->doc($slug) === null) {
            abort(404);
        }
    }

    #[Computed]
    public function coach(): array
    {
        return app(\App\Content\ContentRepository::class)->coach($this->slug);
    }

    public function rendering($view): void
    {
        $view->title($this->coach['title'].' — feras-coach — Feras');
    }
};
?>

<div
    class="mx-auto max-w-3xl"
    x-data="{
        step: 1,
        total: {{ count($this->coach['steps']) }},
        key: 'feras-coach:{{ $slug }}',
        resumed: false,
        init() {
            const url = parseInt(new URLSearchParams(location.search).get('step'), 10);
            const saved = this.load();
            this.step = this.clamp(url || saved.step || 1);
            this.resumed = ! url && ! saved.done && this.step > 1;
            this.$watch('step', () => this.persist());
            this.persist();
        },
        clamp(n) { return Math.min(Math.max(1, n || 1), this.total); },
        load() { try { return JSON.parse(localStorage.getItem(this.key)) ?? {}; } catch (e) { return {}; } },
        persist() {
            const done = this.step > this.total || this.load().done === true;
            try {
                localStorage.setItem(this.key, JSON.stringify({
                    step: Math.min(this.step, this.total), total: this.total, done: done,
                }));
            } catch (e) {}
            const url = new URL(location);
            if (this.step <= this.total) { url.searchParams.set('step', this.step); } else { url.searchParams.delete('step'); }
            history.replaceState(null, '', url);
        },
        next() { if (this.step <= this.total) { this.step++; this.resumed = false; window.scrollTo({ top: 0 }); } },
        back() { if (this.step > 1) { this.step--; window.scrollTo({ top: 0 }); } },
        restart() { this.step = 1; this.resumed = false; },
        bar() { const f = Math.min(this.step, this.total); return '█'.repeat(f) + '░'.repeat(this.total - f); },
    }"
>
    <header class="space-y-3">
        <p class="font-mono text-sm text-ink-500">
            <a href="{{ route('docs.index') }}" wire:navigate class="transition-colors hover:text-ink-300">doc</a>
            <span class="text-ink-600">/</span>
            <a href="{{ route('docs.show', $slug) }}" wire:navigate class="transition-colors hover:text-ink-300">{{ $slug }}</a>
            <span class="text-ink-600">/</span>
            <span class="text-cyan-300">coach</span>
        </p>

        <h1 class="font-mono text-2xl font-semibold text-ink-100 sm:text-3xl">
            <span class="text-emerald-400">$</span> feras-coach {{ $this->coach['title'] }}
        </h1>

        <p class="font-mono text-sm text-emerald-400" aria-live="polite">
            [<span x-text="bar()"></span>] <span x-text="Math.min(step, total)"></span>/{{ count($this->coach['steps']) }}
        </p>
    </header>

    <p x-show="resumed" x-cloak class="mt-6 rounded border border-ink-800 bg-ink-900 px-4 py-2 font-mono text-xs text-ink-400">
        resumed at step <span x-text="step"></span> —
        <button type="button" x-on:click="restart()" class="text-cyan-300 transition-colors hover:text-cyan-200">$ restart</button>
    </p>

    @if ($this->coach['intro'] !== '')
        <div x-show="step === 1" x-cloak class="mt-8">
            <x-prose :html="$this->coach['intro']" />
        </div>
    @endif

    @foreach ($this->coach['steps'] as $i => $s)
        <section x-show="step === {{ $i + 1 }}" x-cloak class="mt-8" wire:key="step-{{ $i }}">
            <h2 class="font-mono text-xl font-semibold text-ink-100">
                <span class="mr-2 text-emerald-400">{{ sprintf('%02d', $i + 1) }}.</span>{{ $s['title'] }}
            </h2>
            <x-prose :html="$s['html']" class="mt-6" />
        </section>
    @endforeach

    <section x-show="step > total" x-cloak class="mt-8 rounded-lg border border-emerald-400/40 bg-emerald-400/5 p-8 text-center font-mono">
        <p class="text-emerald-300">&#10003; walkthrough complete &mdash; exit 0</p>
        <a href="{{ route('docs.show', $slug) }}" wire:navigate class="mt-6 inline-block text-sm text-cyan-300 transition-colors hover:text-cyan-200">
            $ cd ../{{ $slug }}
        </a>
    </section>

    <footer class="mt-12 flex items-center justify-between border-t border-ink-800 pt-6 font-mono text-sm">
        <button type="button" x-show="step > 1 && step <= total" x-cloak x-on:click="back()"
            class="text-ink-400 transition-colors hover:text-ink-100">&larr; back</button>
        <a href="{{ route('docs.show', $slug) }}" wire:navigate x-show="step === 1"
            class="text-ink-400 transition-colors hover:text-ink-100">&larr; cd ../{{ $slug }}</a>
        <button type="button" x-show="step <= total" x-on:click="next()"
            class="rounded border border-emerald-400/40 bg-emerald-400/10 px-4 py-1.5 text-emerald-300 transition-colors hover:bg-emerald-400/20 focus:outline-none focus:ring-2 focus:ring-emerald-400/40">
            <span x-text="step === total ? '$ done' : '$ next'"></span>
        </button>
    </footer>
</div>
