@props(['project', 'branch', 'repo' => null])

@php
    $classes = 'inline-flex items-center gap-1.5 rounded border border-ink-700/80 bg-ink-900 px-2 py-0.5 font-mono text-xs text-cyan-300';
@endphp

@if ($repo)
    <a href="{{ $repo }}" target="_blank" rel="noopener" {{ $attributes->merge(['class' => $classes.' transition-colors hover:border-ink-600 hover:text-cyan-200']) }}>
        <svg viewBox="0 0 16 16" class="h-3.5 w-3.5 shrink-0" fill="currentColor" aria-hidden="true"><path d="M9.5 3.25a2.25 2.25 0 1 1 3 2.122V6A2.5 2.5 0 0 1 10 8.5H6a1 1 0 0 0-1 1v1.128a2.251 2.251 0 1 1-1.5 0V5.372a2.25 2.25 0 1 1 1.5 0v1.836A2.493 2.493 0 0 1 6 7h4a1 1 0 0 0 1-1v-.628A2.25 2.25 0 0 1 9.5 3.25Zm-6 0a.75.75 0 1 0 1.5 0 .75.75 0 0 0-1.5 0Zm8.25-.75a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5ZM4.25 12a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5Z"/></svg>
        <span>{{ $project }}<span class="text-ink-600">:</span>{{ $branch }}</span>
    </a>
@else
    <span {{ $attributes->merge(['class' => $classes]) }}>
        <svg viewBox="0 0 16 16" class="h-3.5 w-3.5 shrink-0" fill="currentColor" aria-hidden="true"><path d="M9.5 3.25a2.25 2.25 0 1 1 3 2.122V6A2.5 2.5 0 0 1 10 8.5H6a1 1 0 0 0-1 1v1.128a2.251 2.251 0 1 1-1.5 0V5.372a2.25 2.25 0 1 1 1.5 0v1.836A2.493 2.493 0 0 1 6 7h4a1 1 0 0 0 1-1v-.628A2.25 2.25 0 0 1 9.5 3.25Zm-6 0a.75.75 0 1 0 1.5 0 .75.75 0 0 0-1.5 0Zm8.25-.75a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5ZM4.25 12a.75.75 0 1 0 0 1.5.75.75 0 0 0 0-1.5Z"/></svg>
        <span>{{ $project }}<span class="text-ink-600">:</span>{{ $branch }}</span>
    </span>
@endif
