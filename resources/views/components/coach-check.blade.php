@props(['slug'])

<span
    x-data
    x-cloak
    x-show="(() => { try { return JSON.parse(localStorage.getItem('feras-coach:{{ $slug }}'))?.done === true } catch (e) { return false } })()"
    {{ $attributes->merge(['class' => 'ml-1 font-mono text-emerald-400']) }}
    title="feras-coach completed"
    aria-label="walkthrough completed"
>&#10003;</span>
