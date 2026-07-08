<?php

use Livewire\Livewire;

it('renders the study index', function () {
    $this->get('/study')
        ->assertOk()
        ->assertSee('study')
        ->assertSee('grep study...');
});

it('renders every seeded study entry', function (string $filename) {
    $slug = preg_replace('/^\d{4}-\d{2}-\d{2}-/', '', $filename);

    $this->get("/study/{$slug}")->assertOk();
})->with(fn () => collect(glob(__DIR__.'/../../resources/content/study/*.md'))
    ->map(fn (string $path) => basename($path, '.md'))
    ->all());

it('returns 404 for an unknown study slug', function () {
    $this->get('/study/unknown-entry')->assertNotFound();
});

it('shows the study nav link with the section active', function () {
    $this->get('/study')->assertSee('./study');
});

it('filters entries by track', function () {
    Livewire::test('study-index')
        ->assertSee('What I&#039;m Studying and Why', false)
        ->call('toggleTrack', 'security-plus')
        ->assertDontSee('What I&#039;m Studying and Why', false)
        ->call('toggleTrack', 'security-plus')
        ->assertSee('What I&#039;m Studying and Why', false);
});

it('filters entries by search with a resettable empty state', function () {
    Livewire::test('study-index')
        ->set('search', 'zzz-no-such-entry')
        ->assertSee('no results for')
        ->call('resetFilters')
        ->assertSee('What I&#039;m Studying and Why', false);
});
