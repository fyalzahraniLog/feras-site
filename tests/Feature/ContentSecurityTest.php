<?php

use App\Content\ContentRepository;

/**
 * The rendered HTML of every content file flows into the unescaped
 * {!! !!} sink in x-prose. These tests pin the CommonMark policy that
 * makes that safe: raw HTML is stripped, unsafe link schemes refused,
 * and code fences still escape their contents.
 */
function hostilePost(): array
{
    $repository = new ContentRepository(base_path('tests/Fixtures/content'));

    return $repository->post('2026-01-01-hostile');
}

it('strips raw html from markdown before it reaches the prose sink', function () {
    $html = hostilePost()['html'];

    expect($html)->not->toContain('<script>')
        ->and($html)->not->toContain('onerror')
        ->and($html)->not->toContain('<img');
});

it('never renders an anchor with an unsafe scheme', function () {
    $html = hostilePost()['html'];

    // CommonMark refuses to linkify javascript:/data: URLs — the markdown
    // stays behind as inert paragraph text instead of becoming an <a href>.
    expect($html)->not->toContain('href="javascript:')
        ->and($html)->not->toMatch('/<a[^>]*javascript:/');
});

it('still renders ordinary markdown and escaped code fences', function () {
    $html = hostilePost()['html'];

    expect($html)->toContain('<strong>markdown</strong>')
        ->and($html)->toContain('&lt;script&gt;a fenced code sample');
});
