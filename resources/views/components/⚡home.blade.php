<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;

new #[Title('Feras — Software Developer')] class extends Component
{
    #[Computed]
    public function posts()
    {
        return app(\App\Content\ContentRepository::class)->posts()->take(3);
    }

    #[Computed]
    public function docs()
    {
        return app(\App\Content\ContentRepository::class)->docs()->take(4);
    }
};
?>

<div class="space-y-20 py-10 sm:space-y-24">

    {{-- ============================================================ --}}
    {{-- 1. HERO --}}
    {{-- ============================================================ --}}
    <section>
        {{-- Fake terminal window --}}
        <div class="overflow-hidden rounded-lg border border-ink-800 bg-ink-900 shadow-lg shadow-black/20">
            {{-- Title bar --}}
            <div class="flex items-center gap-2 border-b border-ink-800 bg-ink-850 px-4 py-3">
                <span class="h-3 w-3 rounded-full bg-red-500/80"></span>
                <span class="h-3 w-3 rounded-full bg-yellow-500/80"></span>
                <span class="h-3 w-3 rounded-full bg-emerald-500/80"></span>
                <span class="ml-3 font-mono text-xs text-ink-500">feras@dev: ~</span>
            </div>
            {{-- Terminal body --}}
            <div class="space-y-3 px-5 py-6 font-mono text-sm sm:px-6 sm:py-8 sm:text-base">
                <p class="text-ink-400">
                    <span class="text-emerald-400">$</span> whoami
                </p>
                {{-- TODO: real CV data --}}
                <h1 class="text-2xl font-bold text-ink-100 sm:text-4xl">Feras</h1>
                <p class="text-lg text-emerald-400 sm:text-xl">Software Developer</p>
                <p class="max-w-2xl text-ink-300">
                    {{-- TODO: real CV data --}}
                    Building pragmatic web software — clean backends, fast interfaces, and tooling that stays out of the way.
                </p>
                <p class="pt-2 text-ink-400">
                    <span class="text-emerald-400">$</span> <span class="inline-block h-4 w-2.5 translate-y-0.5 animate-pulse bg-emerald-400" aria-hidden="true"></span>
                </p>
            </div>
        </div>

        {{-- CTA buttons --}}
        <div class="mt-6 flex flex-wrap items-center gap-4">
            <a href="{{ route('log.index') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-emerald-400 px-5 py-2.5 font-mono text-sm font-semibold text-ink-950 transition-colors hover:bg-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-400/40">
                ./log-dev
            </a>
            <a href="{{ route('docs.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-ink-700 px-5 py-2.5 font-mono text-sm font-semibold text-ink-200 transition-colors hover:border-emerald-400/60 hover:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/40">
                ./doc
            </a>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- 2. ABOUT ME --}}
    {{-- ============================================================ --}}
    <section id="about" class="space-y-8">
        <x-section-heading prefix="//">about-me</x-section-heading>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            {{-- Bio --}}
            <div class="space-y-4 text-ink-300 lg:col-span-2">
                {{-- TODO: real CV data --}}
                <p>
                    I'm a software developer who enjoys the whole stack — from modelling data and shaping
                    APIs to polishing the last pixel of a UI. Most of my day-to-day work happens in the
                    PHP and JavaScript ecosystems, with a soft spot for Laravel and its tooling.
                </p>
                <p>
                    I care about boring reliability: readable code, sensible tests, and deployments that
                    don't require a war room. When something breaks, I'd rather fix the process than just
                    the bug.
                </p>
                <p>
                    Away from the editor I write short dev-log posts about things I've learned, keep a set
                    of living docs for my own setup, and tinker with home-lab experiments.
                </p>
            </div>

            {{-- Quick facts card --}}
            <div class="rounded-lg border border-ink-800 bg-ink-900 p-5">
                <p class="mb-4 font-mono text-xs text-ink-500">$ cat facts.yml</p>
                <dl class="space-y-3 font-mono text-sm">
                    {{-- TODO: real CV data --}}
                    <div class="flex justify-between gap-4">
                        <dt class="text-ink-500">location:</dt>
                        <dd class="text-right text-ink-200">Somewhere, Earth</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-ink-500">focus:</dt>
                        <dd class="text-right text-ink-200">full-stack web</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-ink-500">currently:</dt>
                        <dd class="text-right text-ink-200">building things</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-ink-500">email:</dt>
                        <dd class="text-right">
                            <a href="mailto:hello@example.com" class="text-cyan-300 hover:underline">hello@example.com</a>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- 3. SKILLS --}}
    {{-- ============================================================ --}}
    <section class="space-y-8">
        <x-section-heading prefix="//">skills</x-section-heading>

        {{-- TODO: real CV data --}}
        <div class="space-y-6">
            <div class="space-y-3">
                <h3 class="font-mono text-sm text-ink-500"># Languages</h3>
                <div class="flex flex-wrap gap-2">
                    <x-tag>php</x-tag>
                    <x-tag>typescript</x-tag>
                    <x-tag>javascript</x-tag>
                    <x-tag>sql</x-tag>
                    <x-tag>bash</x-tag>
                </div>
            </div>
            <div class="space-y-3">
                <h3 class="font-mono text-sm text-ink-500"># Frameworks &amp; Tools</h3>
                <div class="flex flex-wrap gap-2">
                    <x-tag>laravel</x-tag>
                    <x-tag>livewire</x-tag>
                    <x-tag>vue</x-tag>
                    <x-tag>tailwindcss</x-tag>
                    <x-tag>mysql</x-tag>
                    <x-tag>redis</x-tag>
                    <x-tag>docker</x-tag>
                </div>
            </div>
            <div class="space-y-3">
                <h3 class="font-mono text-sm text-ink-500"># Practices</h3>
                <div class="flex flex-wrap gap-2">
                    <x-tag>tdd</x-tag>
                    <x-tag>ci/cd</x-tag>
                    <x-tag>code review</x-tag>
                    <x-tag>api design</x-tag>
                    <x-tag>observability</x-tag>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- 4. EXPERIENCE --}}
    {{-- ============================================================ --}}
    <section class="space-y-8">
        <x-section-heading prefix="//">experience</x-section-heading>

        {{-- TODO: real CV data --}}
        <ol class="space-y-10 border-l border-ink-800 pl-6">
            <li class="relative">
                <span class="absolute -left-[1.85rem] top-1.5 h-3 w-3 rounded-full border-2 border-ink-950 bg-emerald-400"></span>
                <p class="font-mono text-xs text-ink-500">2023 — now</p>
                <h3 class="mt-1 text-lg font-semibold text-ink-100">Senior Software Developer</h3>
                <p class="font-mono text-sm text-ink-400">Acme Web Studio</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-ink-300">
                    <li>Leading development of a multi-tenant SaaS platform on Laravel and Livewire.</li>
                    <li>Cut CI pipeline time in half and introduced automated release checklists.</li>
                </ul>
            </li>
            <li class="relative">
                <span class="absolute -left-[1.85rem] top-1.5 h-3 w-3 rounded-full border-2 border-ink-950 bg-emerald-400"></span>
                <p class="font-mono text-xs text-ink-500">2020 — 2023</p>
                <h3 class="mt-1 text-lg font-semibold text-ink-100">Software Developer</h3>
                <p class="font-mono text-sm text-ink-400">Placeholder Systems GmbH</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-ink-300">
                    <li>Built and maintained internal APIs serving several customer-facing products.</li>
                    <li>Migrated a legacy monolith to a modular, test-covered codebase.</li>
                </ul>
            </li>
            <li class="relative">
                <span class="absolute -left-[1.85rem] top-1.5 h-3 w-3 rounded-full border-2 border-ink-950 bg-emerald-400"></span>
                <p class="font-mono text-xs text-ink-500">2018 — 2020</p>
                <h3 class="mt-1 text-lg font-semibold text-ink-100">Junior Developer</h3>
                <p class="font-mono text-sm text-ink-400">Example Agency</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-ink-300">
                    <li>Shipped client websites and small web apps across a range of stacks.</li>
                </ul>
            </li>
        </ol>
    </section>

    {{-- ============================================================ --}}
    {{-- 5. LATEST FROM THE LOG --}}
    {{-- ============================================================ --}}
    <section class="space-y-8">
        <div class="flex items-baseline justify-between gap-4">
            <x-section-heading prefix="//">log-dev</x-section-heading>
            <a href="{{ route('log.index') }}" class="font-mono text-sm text-cyan-300 transition-colors hover:text-cyan-200">
                view all &rarr;
            </a>
        </div>

        <div class="space-y-4">
            @forelse ($this->posts as $post)
                <a href="{{ route('log.show', $post['slug']) }}"
                   wire:key="post-{{ $post['slug'] }}"
                   class="block rounded-lg border border-ink-800 bg-ink-900 p-5 transition-colors hover:border-ink-700">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-baseline sm:gap-6">
                        <span class="shrink-0 font-mono text-xs text-ink-500">{{ $post['date']->format('Y-m-d') }}</span>
                        <div class="min-w-0 flex-1 space-y-1">
                            <h3 class="font-mono text-base font-semibold text-ink-100">{{ $post['title'] }}</h3>
                            <p class="truncate text-sm text-ink-400">{{ $post['excerpt'] }}</p>
                        </div>
                        <div class="flex shrink-0 flex-wrap gap-2">
                            @foreach ($post['tags'] as $tag)
                                <x-tag>{{ $tag }}</x-tag>
                            @endforeach
                        </div>
                    </div>
                </a>
            @empty
                <p class="font-mono text-sm text-ink-500">$ ls ./log-dev &mdash; nothing here yet.</p>
            @endforelse
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- 6. DOC --}}
    {{-- ============================================================ --}}
    <section class="space-y-8">
        <div class="flex items-baseline justify-between gap-4">
            <x-section-heading prefix="//">doc</x-section-heading>
            <a href="{{ route('docs.index') }}" class="font-mono text-sm text-cyan-300 transition-colors hover:text-cyan-200">
                view all &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @forelse ($this->docs as $doc)
                <a href="{{ route('docs.show', $doc['slug']) }}"
                   wire:key="doc-{{ $doc['slug'] }}"
                   class="block rounded-lg border border-ink-800 bg-ink-900 p-5 transition-colors hover:border-ink-700">
                    <p class="mb-2 font-mono text-xs text-emerald-400">{{ $doc['category'] }}</p>
                    <h3 class="font-mono text-base font-semibold text-ink-100">{{ $doc['title'] }}</h3>
                    <p class="mt-2 text-sm text-ink-400">{{ $doc['excerpt'] }}</p>
                </a>
            @empty
                <p class="font-mono text-sm text-ink-500">$ ls ./doc &mdash; nothing here yet.</p>
            @endforelse
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- 7. CONTACT --}}
    {{-- ============================================================ --}}
    <section class="rounded-lg border border-ink-800 bg-ink-900 px-6 py-12 text-center">
        <p class="font-mono text-sm text-ink-400">
            <span class="text-emerald-400">$</span> echo 'get in touch'
        </p>
        {{-- TODO: real CV data --}}
        <a href="mailto:hello@example.com"
           class="mt-4 inline-block font-mono text-lg text-cyan-300 transition-colors hover:text-cyan-200 sm:text-xl">
            hello@example.com
        </a>
        <div class="mt-6 flex items-center justify-center gap-6 font-mono text-sm">
            {{-- TODO: real GitHub / LinkedIn URLs --}}
            <a href="#" class="text-ink-400 transition-colors hover:text-emerald-400">github</a>
            <span class="text-ink-700">/</span>
            <a href="#" class="text-ink-400 transition-colors hover:text-emerald-400">linkedin</a>
        </div>
    </section>

</div>
