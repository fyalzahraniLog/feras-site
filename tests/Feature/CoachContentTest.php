<?php

use App\Content\ContentRepository;

function fixtureRepository(): ContentRepository
{
    return new ContentRepository(base_path('tests/Fixtures/content'));
}

it('splits a walkthrough into intro and steps on h2 boundaries', function () {
    $coach = fixtureRepository()->coach('stepped');

    expect($coach)->not->toBeNull()
        ->and($coach['title'])->toBe('Stepped Walkthrough')
        ->and($coach['intro'])->toContain('what will exist')
        ->and($coach['steps'])->toHaveCount(3)
        ->and(array_column($coach['steps'], 'title'))->toBe([
            'Create the file',
            'Wire it up',
            'Verify it works',
        ]);
});

it('does not split on h2 markers inside fenced code blocks', function () {
    $coach = fixtureRepository()->coach('fenced-hash');

    expect($coach['steps'])->toHaveCount(2)
        ->and($coach['steps'][0]['html'])->toContain('## not-a-step');
});

it('treats a walkthrough with no steps as unusable', function () {
    $repository = fixtureRepository();

    expect($repository->coach('empty'))->not->toBeNull()
        ->and($repository->coach('empty')['steps'])->toBe([])
        ->and($repository->hasCoach('empty'))->toBeFalse();
});

it('returns null for an unknown coach slug', function () {
    expect(fixtureRepository()->coach('nope'))->toBeNull()
        ->and(fixtureRepository()->hasCoach('nope'))->toBeFalse();
});

it('derives the title from the filename when front matter has none', function () {
    expect(fixtureRepository()->coach('no-front-matter')['title'])->toBe('No Front Matter');
});

it('coaches only existing laravel or livewire docs (seed integrity)', function () {
    $repository = app(ContentRepository::class);

    expect($repository->coaches())->not->toBeEmpty();

    foreach ($repository->coaches() as $coach) {
        $doc = $repository->doc($coach['slug']);

        expect($doc)->not->toBeNull("coach '{$coach['slug']}' has no matching doc page")
            ->and(in_array($doc['category'], ['laravel', 'livewire'], true))
            ->toBeTrue("coach '{$coach['slug']}' coaches a '{$doc['category']}' doc");
    }
});

it('keeps every seeded walkthrough code-first with 3-8 steps (seed integrity)', function () {
    foreach (app(ContentRepository::class)->coaches() as $coach) {
        expect(count($coach['steps']))
            ->toBeGreaterThanOrEqual(3, "'{$coach['slug']}' has too few steps")
            ->toBeLessThanOrEqual(8, "'{$coach['slug']}' has too many steps");

        foreach ($coach['steps'] as $i => $step) {
            expect(str_starts_with($step['html'], '<pre'))
                ->toBeTrue('step '.($i + 1)." of '{$coach['slug']}' does not start with a code block");
        }
    }
});
