<?php

declare(strict_types=1);

use Database\Seeders\ArticleSeeder;
use Database\Seeders\PensionFundSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

uses(RefreshDatabase::class);

it('generates sitemap with static, programmatic and database URLs', function () {
    $this->seed(PensionFundSeeder::class);
    $this->seed(ArticleSeeder::class);

    Artisan::call('sitemap:generate');

    $xml = file_get_contents(public_path('sitemap.xml'));

    expect($xml)->toContain('<loc>' . rtrim(config('app.url'), '/') . '</loc>')
        ->and($xml)->toContain('/kalkulacka/vyse')
        ->and($xml)->toContain('/duchod/rocnik/1940')
        ->and($xml)->toContain('/duchod/rocnik/1980')
        ->and($xml)->toContain('/statistiky/praha')
        ->and($xml)->toContain('/pruvodce/starobni-duchod')
        ->and($xml)->toContain('/fondy/cs-dynamicky')
        ->and($xml)->toContain('/magazin/valorizace-2026')
        ->and(substr_count($xml, '<url>'))->toBeGreaterThan(80);
});

it('exports calculation as PDF', function () {
    $response = $this->post('/kalkulacka/vyse/pdf', [
        'birth_date'      => '1962-05-15',
        'gender'          => 'M',
        'children'        => 2,
        'retirement_date' => '2028-07-15',
        'insurance_years' => 40,
        'income_mode'     => 'average',
        'average_monthly_income' => 48833,
    ]);

    $response->assertOk()
        ->assertHeader('content-type', 'application/pdf');

    expect($response->headers->get('content-disposition'))
        ->toContain('vypocet-duchodu-duchody-cz.pdf');
});

it('serves manifest.json and robots.txt with sitemap reference', function () {
    expect(file_exists(public_path('manifest.json')))->toBeTrue()
        ->and(file_get_contents(public_path('robots.txt')))->toContain('Sitemap:');
});
