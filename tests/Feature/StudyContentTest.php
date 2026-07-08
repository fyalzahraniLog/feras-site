<?php

use App\Content\ContentRepository;

it('hydrates the full study-entry schema from front matter', function () {
    $entry = fixtureRepository()->studyEntry('full-matter');

    expect($entry)->not->toBeNull()
        ->and($entry['title'])->toBe('RAG Basics, in My Own Words')
        ->and($entry['track'])->toBe('ibm-genai')
        ->and($entry['module'])->toBe('Course 1 · Module 2')
        ->and($entry['artifact'])->toBe('https://github.com/example/rag-notes')
        ->and($entry['tags'])->toBe(['rag', 'llm'])
        ->and($entry['excerpt'])->toBe('A custom excerpt for the fixture.')
        ->and($entry['date']->format('Y-m-d'))->toBe('2026-07-01');
});

it('strips the date prefix from the filename to build the slug', function () {
    expect(fixtureRepository()->studyEntry('full-matter'))->not->toBeNull();
});

it('applies defaults when front matter is absent', function () {
    $entry = fixtureRepository()->studyEntry('no-front-matter');

    expect($entry)->not->toBeNull()
        ->and($entry['title'])->toBe('No Front Matter')
        ->and($entry['track'])->toBeNull()
        ->and($entry['module'])->toBeNull()
        ->and($entry['artifact'])->toBeNull()
        ->and($entry['tags'])->toBe([])
        ->and($entry['excerpt'])->toContain('study note with no front matter')
        ->and($entry['readingTime'])->toBeGreaterThanOrEqual(1);
});

it('coerces unquoted YAML dates from integer timestamps', function () {
    $entry = fixtureRepository()->studyEntry('unquoted-date');

    expect($entry['date']->format('Y-m-d'))->toBe('2026-06-15');
});

it('sorts study entries newest first', function () {
    $slugs = fixtureRepository()->studyEntries()->pluck('slug');

    expect($slugs)->toHaveCount(3)
        ->and($slugs->search('full-matter'))->toBeLessThan($slugs->search('unquoted-date'));
});

it('returns null for an unknown study slug', function () {
    expect(fixtureRepository()->studyEntry('nope'))->toBeNull();
});

it('keeps every seeded study entry on a known track (seed integrity)', function () {
    $repository = app(ContentRepository::class);

    expect($repository->studyEntries())->not->toBeEmpty();

    foreach ($repository->studyEntries() as $entry) {
        expect(in_array($entry['track'], ContentRepository::STUDY_TRACKS, true))
            ->toBeTrue("study entry '{$entry['slug']}' has invalid track '{$entry['track']}'");

        if ($entry['artifact'] !== null) {
            expect(str_starts_with($entry['artifact'], 'https://'))
                ->toBeTrue("study entry '{$entry['slug']}' has a non-https artifact link");
        }
    }
});
