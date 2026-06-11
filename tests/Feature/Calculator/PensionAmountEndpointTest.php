<?php

declare(strict_types=1);

use App\Models\SavedCalculation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the calculator page', function () {
    $this->get('/kalkulacka/vyse')
        ->assertOk()
        ->assertSee('pension-calculator', false);
});

it('calculates pension with average income mode', function () {
    $response = $this->postJson('/kalkulacka/vyse/spocitat', [
        'birth_date'      => '1962-05-15',
        'gender'          => 'M',
        'children'        => 0,
        'retirement_date' => '2028-07-15',
        'insurance_years' => 40,
        'income_mode'     => 'average',
        'average_monthly_income' => 48833,
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'basicAmount',
            'percentageAmount',
            'childBonus',
            'totalMonthly',
            'personalAssessmentBase',
            'calculationBase',
            'insuranceYears',
            'breakdown',
            'normalRetirementDate',
            'normalRetirementAge' => ['years', 'months'],
            'earlyDays',
        ]);

    expect($response->json('basicAmount'))->toBe(4900)
        ->and($response->json('totalMonthly'))->toBeGreaterThan(10000)
        ->and($response->json('totalMonthly'))->toBeLessThan(40000);
});

it('calculates pension with yearly income mode', function () {
    $yearly = [];
    for ($year = 2000; $year <= 2027; $year++) {
        $yearly[$year] = 480000;
    }

    $response = $this->postJson('/kalkulacka/vyse/spocitat', [
        'birth_date'      => '1962-05-15',
        'gender'          => 'M',
        'children'        => 2,
        'retirement_date' => '2028-07-15',
        'insurance_years' => 40,
        'income_mode'     => 'yearly',
        'yearly_income'   => $yearly,
    ]);

    $response->assertOk();

    expect($response->json('childBonus'))->toBe(1000)
        ->and($response->json('totalMonthly'))->toBeGreaterThan(4900);
});

it('rejects invalid input with Czech validation messages', function () {
    $this->postJson('/kalkulacka/vyse/spocitat', [
        'gender'      => 'X',
        'income_mode' => 'average',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['birth_date', 'gender', 'children', 'retirement_date', 'insurance_years']);
});

it('requires average income when mode is average', function () {
    $this->postJson('/kalkulacka/vyse/spocitat', [
        'birth_date'      => '1962-05-15',
        'gender'          => 'M',
        'children'        => 0,
        'retirement_date' => '2028-07-15',
        'insurance_years' => 40,
        'income_mode'     => 'average',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['average_monthly_income']);
});

it('saves a calculation to the database', function () {
    $response = $this->postJson('/kalkulacka/vyse/ulozit', [
        'input_params' => ['birth_date' => '1962-05-15', 'gender' => 'M'],
        'result'       => ['totalMonthly' => 21500, 'basicAmount' => 4900],
    ]);

    $response->assertCreated()->assertJsonStructure(['id']);

    expect(SavedCalculation::count())->toBe(1);

    $saved = SavedCalculation::first();
    expect($saved->calc_type)->toBe('vyse')
        ->and($saved->result['totalMonthly'])->toBe(21500)
        ->and($saved->session_id)->not->toBeNull();
});
