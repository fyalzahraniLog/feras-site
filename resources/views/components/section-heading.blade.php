@props(['prefix' => '//'])

<h2 {{ $attributes->merge(['class' => 'font-mono text-xl font-semibold text-ink-100 sm:text-2xl']) }}>
    <span class="mr-2 text-emerald-400">{{ $prefix }}</span>{{ $slot }}
</h2>
