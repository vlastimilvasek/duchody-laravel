<?php

declare(strict_types=1);

use Database\Seeders\PensionStatisticSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(PensionStatisticSeeder::class);
});

it('renders the statistics dashboard with metric cards and chart island', function () {
    $this->get('/statistiky')
        ->assertOk()
        ->assertSee('Statistiky důchodů v ČR')
        ->assertSee('stats-dashboard', false)
        ->assertSee('21 400 Kč')
        ->assertSee('Hlavní město Praha')
        ->assertSee('Dataset', false);
});

it('renders a region detail page with comparison and trend chart', function () {
    $this->get('/statistiky/praha')
        ->assertOk()
        ->assertSee('Hlavní město Praha')
        ->assertSee('Srovnání s celostátním průměrem')
        ->assertSee('region-trend-chart', false)
        ->assertSee('BreadcrumbList', false);
});

it('shows negative difference for below-average region', function () {
    $this->get('/statistiky/karlovarsky')
        ->assertOk()
        ->assertSee('Karlovarský kraj')
        ->assertSee('méně');
});

it('returns 404 for unknown region slug', function () {
    $this->get('/statistiky/neexistujici-kraj')->assertNotFound();
});
