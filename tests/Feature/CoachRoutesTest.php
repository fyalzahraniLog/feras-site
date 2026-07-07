<?php

use App\Content\ContentRepository;
use Livewire\Livewire;

it('renders the coach page for a coached doc', function () {
    $this->get('/docs/routing/coach')
        ->assertOk()
        ->assertSee('feras-coach')
        ->assertSee('$ next');
});

it('renders every seeded walkthrough', function (string $slug) {
    $this->get("/docs/{$slug}/coach")->assertOk();
})->with(fn () => collect(glob(__DIR__.'/../../resources/content/coach/*.md'))
    ->map(fn (string $path) => basename($path, '.md'))
    ->all());

it('returns 404 for a coach of an unknown doc', function () {
    $this->get('/docs/unknown/coach')->assertNotFound();
});

it('returns 404 for a doc page without a walkthrough', function () {
    $this->get('/docs/getting-started/coach')->assertNotFound();
});

it('shows the coach button only on coached doc pages', function () {
    $this->get('/docs/routing')->assertSee('feras-coach --learn');
    $this->get('/docs/getting-started')->assertDontSee('feras-coach --learn');
});

it('pre-renders every step for client-side stepping', function () {
    $coach = app(ContentRepository::class)->coach('routing');

    $component = Livewire::test('docs-coach', ['slug' => 'routing']);

    foreach ($coach['steps'] as $step) {
        $component->assertSee($step['title']);
    }
});
