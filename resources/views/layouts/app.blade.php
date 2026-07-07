<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <script>
            // Apply the saved color theme before first paint (no flash).
            (function () { try { var t = localStorage.getItem('feras-theme'); if (t) { document.documentElement.dataset.theme = t; } } catch (e) {} })();
        </script>

        <title>{{ $title ?? config('app.name') }}</title>
        <meta name="description" content="{{ $description ?? 'Feras Alzahrani — Full-Stack Developer (React, Inertia, Laravel, Tailwind). CV, projects, dev log, and documentation.' }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @production
            <script defer src="/_vercel/insights/script.js"></script>
        @endproduction

        @livewireStyles
    </head>
    <body class="flex min-h-screen flex-col bg-ink-950 font-sans text-ink-200 antialiased">
        <div class="bg-grid-overlay pointer-events-none fixed inset-0" aria-hidden="true" data-grid-static></div>

        <canvas id="blackhole-canvas" class="pointer-events-none fixed inset-0 h-full w-full" aria-hidden="true" data-ball-scale="{{ request()->routeIs('home') ? '1' : '0.6' }}"></canvas>

        <header class="sticky top-0 z-40 border-b border-ink-800/80 bg-ink-950/85 backdrop-blur-sm">
            <nav class="mx-auto flex h-14 w-full max-w-5xl items-center justify-between px-4 sm:px-6">
                <a href="{{ route('home') }}" class="font-mono text-xs sm:text-sm" aria-label="Home">
                    <span class="text-emerald-400">feras</span><span class="text-ink-500">@dev</span><span class="text-ink-500">:~$</span><span class="ml-1 inline-block h-3.5 w-2 animate-pulse bg-emerald-400/80 align-middle" aria-hidden="true"></span>
                </a>

                @php
                    $navLink = fn (bool $active) => $active
                        ? 'rounded-md bg-emerald-400/10 px-2.5 py-1.5 text-emerald-300'
                        : 'rounded-md px-2.5 py-1.5 text-ink-400 transition-colors hover:bg-ink-800/60 hover:text-ink-100';
                @endphp

                <div class="flex items-center gap-0.5 font-mono text-xs sm:gap-1 sm:text-sm">
                    {{-- Theme switcher: ink / ember / nebula --}}
                    <div
                        x-data="{
                            themes: [
                                { id: 'ink', color: '#34d399', label: 'ink — emerald on blue-black' },
                                { id: 'ember', color: '#fbbf24', label: 'ember — amber on warm charcoal' },
                                { id: 'nebula', color: '#f472b6', label: 'nebula — pink on deep purple' },
                            ],
                            current: document.documentElement.dataset.theme || 'ink',
                            set(id) {
                                this.current = id;
                                try {
                                    if (id === 'ink') { delete document.documentElement.dataset.theme; localStorage.removeItem('feras-theme'); }
                                    else { document.documentElement.dataset.theme = id; localStorage.setItem('feras-theme', id); }
                                } catch (e) {}
                                window.__blackhole?.destroy(); window.__blackhole?.init();
                            },
                        }"
                        class="mr-2 flex items-center gap-1.5"
                        role="radiogroup"
                        aria-label="Color theme"
                    >
                        <template x-for="t in themes" :key="t.id">
                            <button
                                type="button"
                                role="radio"
                                x-on:click="set(t.id)"
                                :aria-label="t.label"
                                :aria-checked="(current === t.id).toString()"
                                :class="current === t.id ? 'scale-110 border-ink-100/70' : 'border-transparent opacity-60 hover:opacity-100'"
                                :style="`background-color: ${t.color}`"
                                class="h-3 w-3 rounded-full border transition hover:scale-125 focus:outline-none focus:ring-2 focus:ring-emerald-400/40"
                            ></button>
                        </template>
                    </div>

                    <a href="{{ route('home') }}" class="{{ $navLink(request()->routeIs('home')) }}">./about</a>
                    <a href="{{ route('log.index') }}" class="{{ $navLink(request()->routeIs('log.*')) }}">./log-dev</a>
                    <a href="{{ route('docs.index') }}" class="{{ $navLink(request()->routeIs('docs.*')) }}">./doc</a>
                </div>
            </nav>
        </header>

        <main class="relative z-10 mx-auto w-full max-w-5xl flex-1 px-4 py-10 sm:px-6 sm:py-14">
            {{ $slot }}
        </main>

        <footer class="relative z-10 border-t border-ink-800/80">
            <div class="mx-auto flex w-full max-w-5xl flex-col gap-2 px-4 py-6 font-mono text-xs text-ink-500 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <p><span class="text-emerald-400">$</span> echo "&copy; {{ date('Y') }} Feras Alzahrani"</p>
                <a href="mailto:Fyalzahrani@hotmail.com" class="text-cyan-300 transition-colors hover:text-cyan-200">Fyalzahrani@hotmail.com</a>
                <p class="text-ink-600">built with Laravel + Livewire &middot; exit 0</p>
            </div>
        </footer>

        @livewireScripts
    </body>
</html>
