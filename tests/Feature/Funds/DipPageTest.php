<?php

declare(strict_types=1);

it('renders the DIP page with providers, calculator and disclaimer', function () {
    $this->get('/dip')
        ->assertOk()
        ->assertSee('Dlouhodobý investiční produkt')
        ->assertSee('dip-tax-calculator', false)
        ->assertSee('Portu')
        ->assertSee('XTB')
        ->assertSee('může získat provizi')
        ->assertSee('FAQPage', false);
});
