@props(['html'])

<div {{ $attributes->merge(['class' => 'prose prose-invert max-w-none prose-headings:font-mono prose-headings:text-ink-100 prose-p:text-ink-300 prose-li:text-ink-300 prose-strong:text-ink-100 prose-a:text-cyan-300 prose-a:no-underline hover:prose-a:underline prose-code:text-emerald-300 prose-code:before:content-none prose-code:after:content-none prose-pre:border prose-pre:border-ink-800 prose-pre:bg-ink-900 prose-blockquote:border-emerald-400/60 prose-blockquote:text-ink-400 prose-hr:border-ink-800']) }}>
    {!! $html !!}
</div>
