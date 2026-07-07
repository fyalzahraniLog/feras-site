<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;

new #[Title('Feras Alzahrani — Full-Stack Developer')] class extends Component
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
                <h1 class="text-2xl font-bold text-ink-100 sm:text-4xl">Feras Alzahrani</h1>
                <p class="text-lg text-emerald-400 sm:text-xl">Full-Stack Developer</p>
                <p class="max-w-2xl text-ink-300">
                    Computer Science graduate building clean, scalable, user-focused web apps
                    with the RILT stack &mdash; React, Inertia, Laravel, Tailwind.
                </p>
                <p class="pt-2 text-ink-400">
                    <span class="text-emerald-400">$</span> <span class="inline-block h-4 w-2.5 translate-y-0.5 animate-pulse bg-emerald-400" aria-hidden="true"></span>
                </p>
            </div>
        </div>

        {{-- CTA buttons --}}
        <div class="mt-6 flex flex-wrap items-center gap-4">
            <a href="#projects"
               class="inline-flex items-center gap-2 rounded-lg bg-emerald-400 px-5 py-2.5 font-mono text-sm font-semibold text-ink-950 transition-colors hover:bg-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-400/40">
                ./projects
            </a>
            <a href="{{ route('log.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-ink-700 px-5 py-2.5 font-mono text-sm font-semibold text-ink-200 transition-colors hover:border-emerald-400/60 hover:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-400/40">
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

        <div class="grid grid-cols-1 gap-8">
            {{-- Bio --}}
            <div class="max-w-3xl space-y-4 text-ink-300">
                <p>
                    I'm a Computer Science graduate from Umm Al-Qura University and a full-stack
                    developer working with the RILT stack &mdash; React, Inertia, Laravel, and
                    Tailwind CSS.
                </p>
                <p>
                    I've built production-ready web apps with full CRUD, authentication, and
                    responsive UIs &mdash; from task-management platforms deployed on Laravel Cloud
                    to a React SPA with ~50 reusable components. A background in UI/UX research
                    means I care as much about the user flow as about the code behind it.
                </p>
                <p>
                    Currently I work as an IT Network Technician with Harf Information Technology
                    and co-develop the front-end of PcHome, a browser mini-games platform. I'm
                    looking for a full-stack developer role building clean, scalable, user-focused
                    web applications.
                </p>
            </div>

        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- 3. PROJECTS --}}
    {{-- ============================================================ --}}
    <section id="projects" class="space-y-8">
        <x-section-heading prefix="//">projects</x-section-heading>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

            {{-- PcHome --}}
            <div class="flex flex-col rounded-lg border border-ink-800 bg-ink-900 p-5">
                <div class="flex items-baseline justify-between gap-3">
                    <h3 class="font-mono text-base font-semibold text-ink-100">PcHome</h3>
                    <span class="rounded border border-emerald-400/40 bg-emerald-400/10 px-2 py-0.5 font-mono text-xs text-emerald-300">in progress</span>
                </div>
                <p class="mt-1 font-mono text-xs text-emerald-400">co-developer (front-end)</p>
                <p class="mt-3 flex-1 text-sm text-ink-300">
                    Web platform hosting browser-based mini-games. Co-developing the front-end
                    with reusable UI components on the RILT stack.
                </p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <x-tag>react</x-tag>
                    <x-tag>inertia</x-tag>
                    <x-tag>laravel</x-tag>
                    <x-tag>tailwind</x-tag>
                </div>
            </div>

            {{-- Laravel RILT Stack --}}
            <div class="flex flex-col rounded-lg border border-ink-800 bg-ink-900 p-5">
                <div class="flex items-baseline justify-between gap-3">
                    <h3 class="font-mono text-base font-semibold text-ink-100">Laravel RILT Stack</h3>
                    <span class="font-mono text-xs text-ink-500">2026-03</span>
                </div>
                <p class="mt-1 font-mono text-xs text-emerald-400">personal project</p>
                <p class="mt-3 flex-1 text-sm text-ink-300">
                    Task &amp; media management app: React/Inertia front-end in TypeScript, 4 Eloquent
                    models with a many-to-many pivot, Fortify auth with 2FA and a full account
                    settings area, and multi-image upload via Spatie Media Library with responsive
                    conversions and paginated CRUD.
                </p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <x-tag>typescript</x-tag>
                    <x-tag>react</x-tag>
                    <x-tag>laravel</x-tag>
                    <x-tag>fortify</x-tag>
                    <x-tag>spatie</x-tag>
                </div>
                <div class="mt-4 flex gap-4 font-mono text-xs">
                    <a href="https://github.com/fyalzahraniLog/laravel-RILTstack" target="_blank" rel="noopener" class="text-cyan-300 transition-colors hover:text-cyan-200">[github]</a>
                    <a href="https://laravel-riltstack-master-ngq7l5.laravel.cloud/" target="_blank" rel="noopener" class="text-cyan-300 transition-colors hover:text-cyan-200">[live demo]</a>
                </div>
            </div>

            {{-- employTask (Tuwaiq Academy) --}}
            <div class="flex flex-col rounded-lg border border-ink-800 bg-ink-900 p-5">
                <div class="flex items-baseline justify-between gap-3">
                    <h3 class="font-mono text-base font-semibold text-ink-100">employTask</h3>
                    <span class="font-mono text-xs text-ink-500">2025-04 &rarr; 2025-05</span>
                </div>
                <p class="mt-1 font-mono text-xs text-emerald-400">Tuwaiq Academy &mdash; Laravel bootcamp</p>
                <p class="mt-3 flex-1 text-sm text-ink-300">
                    Full-stack task-management app deployed to Laravel Cloud: relational schema
                    (users, groups, tasks) where tasks belong to groups and are assigned to users,
                    complete CRUD, Breeze authentication, and a responsive Tailwind UI.
                </p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <x-tag>laravel</x-tag>
                    <x-tag>breeze</x-tag>
                    <x-tag>tailwind</x-tag>
                    <x-tag>mysql</x-tag>
                </div>
                <div class="mt-4 flex gap-4 font-mono text-xs">
                    <a href="https://github.com/fyalzahraniLog/employTask" target="_blank" rel="noopener" class="text-cyan-300 transition-colors hover:text-cyan-200">[github]</a>
                    <a href="https://employtask-main-fukehc.laravel.cloud/" target="_blank" rel="noopener" class="text-cyan-300 transition-colors hover:text-cyan-200">[live demo]</a>
                </div>
            </div>

            {{-- Project React --}}
            <div class="flex flex-col rounded-lg border border-ink-800 bg-ink-900 p-5">
                <div class="flex items-baseline justify-between gap-3">
                    <h3 class="font-mono text-base font-semibold text-ink-100">Project React</h3>
                    <span class="font-mono text-xs text-ink-500">2025-02 &rarr; 2025-03</span>
                </div>
                <p class="mt-1 font-mono text-xs text-emerald-400">React single-page application</p>
                <p class="mt-3 flex-1 text-sm text-ink-300">
                    React SPA with ~50 reusable components and 8 custom hooks &mdash; effect-based
                    data fetching, forms with authentication, state management, and performance
                    optimization to minimize unnecessary re-renders.
                </p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <x-tag>react</x-tag>
                    <x-tag>hooks</x-tag>
                    <x-tag>spa</x-tag>
                </div>
                <div class="mt-4 flex gap-4 font-mono text-xs">
                    <a href="https://github.com/fyalzahraniLog/project-react-final-code" target="_blank" rel="noopener" class="text-cyan-300 transition-colors hover:text-cyan-200">[github]</a>
                    <a href="https://project-react-final-code.vercel.app/" target="_blank" rel="noopener" class="text-cyan-300 transition-colors hover:text-cyan-200">[live demo]</a>
                </div>
            </div>

            {{-- ScoopCritic --}}
            <div class="flex flex-col rounded-lg border border-ink-800 bg-ink-900 p-5">
                <div class="flex items-baseline justify-between gap-3">
                    <h3 class="font-mono text-base font-semibold text-ink-100">ScoopCritic</h3>
                    <span class="font-mono text-xs text-ink-500">2025-06</span>
                </div>
                <p class="mt-1 font-mono text-xs text-emerald-400">UI/UX designer &amp; user researcher</p>
                <p class="mt-3 flex-1 text-sm text-ink-300">
                    End-to-end design of a game-rating platform: user research on existing platforms
                    to identify UX gaps, and high-fidelity Figma prototypes that streamline
                    navigation across game listings, news, and release dates.
                </p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <x-tag>figma</x-tag>
                    <x-tag>ui/ux</x-tag>
                    <x-tag>user research</x-tag>
                </div>
                <div class="mt-4 flex gap-4 font-mono text-xs">
                    <a href="https://www.figma.com/proto/0UvxeniXKI5QemnagMRZ8b/portfolio?node-id=291-10617&t=mNGboqsedQ2TASnW-1" target="_blank" rel="noopener" class="text-cyan-300 transition-colors hover:text-cyan-200">[figma prototype]</a>
                </div>
            </div>

            {{-- Graduation project --}}
            <div class="flex flex-col rounded-lg border border-ink-800 bg-ink-900 p-5">
                <div class="flex items-baseline justify-between gap-3">
                    <h3 class="font-mono text-base font-semibold text-ink-100">Front-End Website</h3>
                    <span class="font-mono text-xs text-ink-500">2022-09 &rarr; 2023-03</span>
                </div>
                <p class="mt-1 font-mono text-xs text-emerald-400">university graduation project</p>
                <p class="mt-3 flex-1 text-sm text-ink-300">
                    Designed and developed the landing page for a scientific study in HTML and CSS,
                    including wireframes and prototypes for user flow and usability.
                </p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <x-tag>html</x-tag>
                    <x-tag>css</x-tag>
                    <x-tag>wireframing</x-tag>
                </div>
                <div class="mt-4 flex gap-4 font-mono text-xs">
                    <a href="https://www.figma.com/proto/ZpBvApGQghG7aDUxEWmKtq/Writtiner-Design?page-id=0%3A1&node-id=1-3&viewport=628%2C139%2C0.16&t=FsGglUvmRIXVAkNs-1&scaling=min-zoom&content-scaling=fixed" target="_blank" rel="noopener" class="text-cyan-300 transition-colors hover:text-cyan-200">[figma prototype]</a>
                </div>
            </div>

        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- 4. EXPERIENCE --}}
    {{-- ============================================================ --}}
    <section class="space-y-8">
        <x-section-heading prefix="//">experience</x-section-heading>

        <ol class="space-y-10 border-l border-ink-800 pl-6">
            <li class="relative">
                <span class="absolute -left-[1.85rem] top-1.5 h-3 w-3 rounded-full border-2 border-ink-950 bg-emerald-400"></span>
                <p class="font-mono text-xs text-ink-500">2026-01 — now</p>
                <h3 class="mt-1 text-lg font-semibold text-ink-100">IT Network Technician</h3>
                <p class="font-mono text-sm text-ink-400">Harf Information Technology &mdash; College of IT, Umm Al-Qura University</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-ink-300">
                    <li>Extend LAN cabling from network points to switches, connecting 2&ndash;4 devices per request to expand reliable network coverage across the college.</li>
                    <li>Resolve 5&ndash;7 help-desk tickets per week, troubleshooting hardware, software, and network connectivity issues for staff and students.</li>
                </ul>
            </li>
        </ol>
    </section>

    {{-- ============================================================ --}}
    {{-- 5. EDUCATION & COURSES --}}
    {{-- ============================================================ --}}
    <section class="space-y-8">
        <x-section-heading prefix="//">education</x-section-heading>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="rounded-lg border border-ink-800 bg-ink-900 p-5">
                <p class="font-mono text-xs text-ink-500">2019 &rarr; 2025</p>
                <h3 class="mt-1 text-lg font-semibold text-ink-100">B.Sc. Computer Science</h3>
                <p class="font-mono text-sm text-ink-400">Umm Al-Qura University</p>
            </div>

            <div class="rounded-lg border border-ink-800 bg-ink-900 p-5">
                <p class="mb-3 font-mono text-xs text-ink-500"># Courses &amp; certifications &mdash; Udemy, 2022&ndash;2024</p>
                <ul class="space-y-1.5 font-mono text-sm text-ink-300">
                    <li><span class="text-emerald-400">-</span> Complete Web &amp; Mobile Designer: UI/UX, Figma</li>
                    <li><span class="text-emerald-400">-</span> Advanced CSS &amp; SASS: Flexbox, Grid, Animations</li>
                    <li><span class="text-emerald-400">-</span> The Git &amp; GitHub Bootcamp</li>
                    <li><span class="text-emerald-400">-</span> The Web Developer Bootcamp 2024</li>
                    <li><span class="text-emerald-400">-</span> Clean Code</li>
                </ul>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- 6. SKILLS --}}
    {{-- ============================================================ --}}
    <section class="space-y-8">
        <x-section-heading prefix="//">skills</x-section-heading>

        <div class="space-y-6">
            <div class="space-y-3">
                <h3 class="font-mono text-sm text-ink-500"># Languages</h3>
                <div class="flex flex-wrap gap-2">
                    <x-tag>html5</x-tag>
                    <x-tag>css3</x-tag>
                    <x-tag>javascript (es6)</x-tag>
                    <x-tag>typescript</x-tag>
                    <x-tag>php</x-tag>
                    <x-tag>sql (mysql)</x-tag>
                </div>
            </div>
            <div class="space-y-3">
                <h3 class="font-mono text-sm text-ink-500"># Frameworks &amp; Libraries</h3>
                <div class="flex flex-wrap gap-2">
                    <x-tag>react.js</x-tag>
                    <x-tag>laravel</x-tag>
                    <x-tag>inertia.js</x-tag>
                    <x-tag>tailwind css</x-tag>
                    <x-tag>shadcn/ui</x-tag>
                    <x-tag>eloquent orm</x-tag>
                    <x-tag>laravel breeze</x-tag>
                    <x-tag>laravel fortify</x-tag>
                    <x-tag>spatie media library</x-tag>
                </div>
            </div>
            <div class="space-y-3">
                <h3 class="font-mono text-sm text-ink-500"># Concepts</h3>
                <div class="flex flex-wrap gap-2">
                    <x-tag>rest apis</x-tag>
                    <x-tag>crud</x-tag>
                    <x-tag>authentication</x-tag>
                    <x-tag>responsive design</x-tag>
                    <x-tag>component-based architecture</x-tag>
                    <x-tag>state management</x-tag>
                    <x-tag>spa</x-tag>
                </div>
            </div>
            <div class="space-y-3">
                <h3 class="font-mono text-sm text-ink-500"># Design &amp; Research</h3>
                <div class="flex flex-wrap gap-2">
                    <x-tag>figma</x-tag>
                    <x-tag>prototyping</x-tag>
                    <x-tag>wireframing</x-tag>
                    <x-tag>ui/ux</x-tag>
                    <x-tag>user research</x-tag>
                </div>
            </div>
            <div class="space-y-3">
                <h3 class="font-mono text-sm text-ink-500"># Tools</h3>
                <div class="flex flex-wrap gap-2">
                    <x-tag>git</x-tag>
                    <x-tag>github</x-tag>
                    <x-tag>laravel cloud</x-tag>
                    <x-tag>vs code</x-tag>
                    <x-tag>ai coding tools (claude code, codex, gemini)</x-tag>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================================================ --}}
    {{-- 7. LATEST FROM THE LOG --}}
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
    {{-- 8. DOC --}}
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
    {{-- 9. CONTACT --}}
    {{-- ============================================================ --}}
    <section class="rounded-lg border border-ink-800 bg-ink-900 px-6 py-12 text-center">
        <p class="font-mono text-sm text-ink-400">
            <span class="text-emerald-400">$</span> echo 'get in touch'
        </p>
        <a href="mailto:Fyalzahrani@hotmail.com"
           class="mt-4 inline-block font-mono text-lg text-cyan-300 transition-colors hover:text-cyan-200 sm:text-xl">
            Fyalzahrani@hotmail.com
        </a>
        <div class="mt-6 flex items-center justify-center gap-6 font-mono text-sm">
            <a href="https://github.com/fyalzahraniLog" target="_blank" rel="noopener" class="text-ink-400 transition-colors hover:text-emerald-400">github</a>
            <span class="text-ink-700">/</span>
            <a href="https://www.linkedin.com/in/feras-al-zahrani-04743a2a4" target="_blank" rel="noopener" class="text-ink-400 transition-colors hover:text-emerald-400">linkedin</a>
        </div>
    </section>

</div>
