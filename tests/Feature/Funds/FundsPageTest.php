<?php

declare(strict_types=1);

use App\Models\PensionFund;
use Database\Seeders\PensionFundSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(PensionFundSeeder::class);
});

it('renders the funds comparison page with table island and disclaimer', function () {
    $this->get('/fondy')
        ->assertOk()
        ->assertSee('funds-table', false)
        ->assertSee('Srovnání penzijních fondů')
        ->assertSee('může získat provizi')
        ->assertSee('ItemList', false);
});

it('passes funds data as JSON props to the Vue island', function () {
    $response = $this->get('/fondy');

    $response->assertOk()
        ->assertSee('data-props', false)
        ->assertSee('Conseq globální akciový účastnický fond');
});

it('renders the fund detail page with chart, rating and affiliate CTA', function () {
    $fund = PensionFund::where('slug', 'cs-dynamicky')->firstOrFail();

    $this->get('/fondy/cs-dynamicky')
        ->assertOk()
        ->assertSee($fund->name)
        ->assertSee('fund-chart', false)
        ->assertSee('Naše hodnocení')
        ->assertSee('FinancialProduct', false)
        ->assertSee('BreadcrumbList', false)
        ->assertSee('může získat provizi');
});

it('returns 404 for unknown fund slug', function () {
    $this->get('/fondy/neexistujici-fond')->assertNotFound();
});
