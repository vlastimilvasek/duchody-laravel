<?php

declare(strict_types=1);

it('renders the guides index with all four guides', function () {
    $this->get('/pruvodce')
        ->assertOk()
        ->assertSee('Průvodci důchodem')
        ->assertSee('/pruvodce/starobni-duchod', false)
        ->assertSee('/pruvodce/predcasny-duchod', false)
        ->assertSee('/pruvodce/penzijni-sporeni', false)
        ->assertSee('/pruvodce/duchodova-reforma-2025', false);
});

it('renders each guide with structured data and disclaimer', function (string $slug, string $expected) {
    $this->get("/pruvodce/{$slug}")
        ->assertOk()
        ->assertSee($expected)
        ->assertSee('BreadcrumbList', false)
        ->assertSee('Upozornění');
})->with([
    ['starobni-duchod', 'Podmínky nároku'],
    ['predcasny-duchod', 'Jak se počítá krácení'],
    ['penzijni-sporeni', 'Státní příspěvek'],
    ['duchodova-reforma-2025', 'Proč reforma vznikla'],
]);

it('returns 404 for unknown guide', function () {
    $this->get('/pruvodce/neexistujici')->assertNotFound();
});
