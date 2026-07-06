<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>
        <meta name="description" content="{{ $description ?? 'Feras Alzahrani — Full-Stack Developer (React, Inertia, Laravel, Tailwind). CV, projects, dev log, and documentation.' }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

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
                <p class="text-ink-600">built with Laravel + Livewire &middot; exit 0</p>
            </div>
        </footer>

        @livewireScripts
    </body>
</html>
