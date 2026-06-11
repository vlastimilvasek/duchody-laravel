<?php

declare(strict_types=1);

use App\Services\Content\MarkdownRenderer;
use Database\Seeders\ArticleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(ArticleSeeder::class);
});

it('renders the magazine listing with category filters', function () {
    $this->get('/magazin')
        ->assertOk()
        ->assertSee('Magazín')
        ->assertSee('Valorizace důchodů 2026')
        ->assertSee('Aktuality')
        ->assertSee('Blog', false);
});

it('filters articles by category', function () {
    $this->get('/magazin?kategorie=data')
        ->assertOk()
        ->assertSee('Průměrný důchod podle krajů')
        ->assertDontSee('Valorizace důchodů 2026: Průměrný důchod vzroste');
});

it('returns 404 for unknown category', function () {
    $this->get('/magazin?kategorie=neexistujici')->assertNotFound();
});

it('searches articles by title', function () {
    $this->get('/magazin?q=valorizace')
        ->assertOk()
        ->assertSee('Valorizace důchodů 2026');
});

it('shows empty state when search finds nothing', function () {
    $this->get('/magazin?q=xyzneexistuje')
        ->assertOk()
        ->assertSee('Nenašli jsme žádné články');
});

it('renders the article detail with markdown, boxes and related articles', function () {
    $this->get('/magazin/valorizace-2026')
        ->assertOk()
        ->assertSee('Valorizace důchodů 2026')
        ->assertSee('Co se mění od ledna 2026')
        // [info] shortcode → modrý box
        ->assertSee('border-blue-300', false)
        // [warning] shortcode → žlutý box
        ->assertSee('border-amber-400', false)
        // [kalkulacka] shortcode → CTA
        ->assertSee('Kolik budete mít důchod vy?')
        ->assertSee('Mohlo by vás zajímat')
        ->assertSee('Redakce Důchody.cz')
        ->assertSee('BreadcrumbList', false);
});

it('returns 404 for unpublished or unknown article', function () {
    $this->get('/magazin/neexistujici-clanek')->assertNotFound();
});

it('converts markdown tables and strips raw html', function () {
    $renderer = new MarkdownRenderer();

    $html = $renderer->render("| A | B |\n|---|---|\n| 1 | 2 |\n\n<script>alert(1)</script>")->toHtml();

    expect($html)->toContain('<table>')
        ->and($html)->not->toContain('<script>alert');
});
