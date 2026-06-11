<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

it('renders a rocnik page with table, FAQ and related years', function () {
    $this->get('/duchod/rocnik/1965')
        ->assertOk()
        ->assertSee('Důchod pro ročník 1965')
        ->assertSee('Žena — 2 děti')
        ->assertSee('Časté otázky')
        ->assertSee('FAQPage', false)
        ->assertSee('/duchod/rocnik/1964', false)
        ->assertSee('/duchod/rocnik/1970', false);
});

it('shows child reduction answer only for pre-1966 cohorts', function () {
    $this->get('/duchod/rocnik/1960')->assertOk()->assertSee('snížený o 4 měsíce');
    $this->get('/duchod/rocnik/1975')->assertOk()->assertSee('výchovné');
});

it('returns 404 outside the 1940-1980 range', function () {
    $this->get('/duchod/rocnik/1939')->assertNotFound();
    $this->get('/duchod/rocnik/1981')->assertNotFound();
    $this->get('/duchod/rocnik/abc')->assertNotFound();
});

it('caches the computed data', function () {
    $this->get('/duchod/rocnik/1962')->assertOk();

    expect(Cache::has('duchod.rocnik.1962'))->toBeTrue();
});
