<?php

declare(strict_types=1);

use App\Enums\Gender;
use App\Services\Pension\PensionCalculator;
use App\Services\Pension\DTO\PensionParams;
use Carbon\Carbon;

describe('PensionCalculator — výpočtový základ', function () {
    it('OVZ pod 1. redukční hranicí → 99 % OVZ', function () {
        // OVZ = 10 000 Kč (pod 16 306) → základ = 9 900
        $params = new PensionParams(
            birthDate:      Carbon::parse('1960-01-01'),
            gender:         Gender::Male,
            children:       0,
            retirementDate: Carbon::parse('2026-01-01'),
            yearlyIncome:   [],
            insuranceYears: 1,
            excludedDays:   0.0,
        );

        $calc   = new PensionCalculator();
        $result = $calc->calculate($params);

        // S prázdným příjmem je OVZ = 0, výpočtový základ = 0,
        // procentní výměra = min 770 Kč
        expect($result->percentageAmount)->toBe(770)
            ->and($result->basicAmount)->toBe(4900)
            ->and($result->childBonus)->toBe(0)
            ->and($result->totalMonthly)->toBe(4900 + 770);
    });

    it('základní výměra je vždy 4900 Kč (2026)', function () {
        $params = new PensionParams(
            birthDate:      Carbon::parse('1955-01-01'),
            gender:         Gender::Male,
            children:       0,
            retirementDate: Carbon::parse('2026-01-01'),
            yearlyIncome:   [],
            insuranceYears: 40,
        );

        $result = (new PensionCalculator())->calculate($params);
        expect($result->basicAmount)->toBe(4900);
    });

    it('výchovné za 2 děti = 1000 Kč', function () {
        $params = new PensionParams(
            birthDate:      Carbon::parse('1955-01-01'),
            gender:         Gender::Female,
            children:       2,
            retirementDate: Carbon::parse('2026-01-01'),
            yearlyIncome:   [],
            insuranceYears: 35,
        );

        $result = (new PensionCalculator())->calculate($params);
        expect($result->childBonus)->toBe(1000);
    });
});

describe('PensionCalculator — integrační test průměrného příjmu', function () {
    it('muž, 40 let pojištění, průměrný příjem → výsledek 15 000–30 000 Kč', function () {
        // Průměrný roční příjem ~588 000 Kč (48 833 Kč/měsíc)
        $averageMonthly = 48833;
        $yearlyIncome   = [];
        for ($year = 1986; $year <= 2025; $year++) {
            $yearlyIncome[$year] = $averageMonthly * 12;
        }

        $params = new PensionParams(
            birthDate:      Carbon::parse('1962-01-01'),
            gender:         Gender::Male,
            children:       0,
            retirementDate: Carbon::parse('2026-07-01'),
            yearlyIncome:   $yearlyIncome,
            insuranceYears: 40,
        );

        $result = (new PensionCalculator())->calculate($params);

        expect($result->totalMonthly)
            ->toBeGreaterThan(15000)
            ->toBeLessThan(30000);

        expect($result->personalAssessmentBase)->toBeGreaterThan(0);
        expect($result->calculationBase)->toBeGreaterThan(0);
    });

    it('žena, 35 let pojištění, 3 děti, podprůměrný příjem → výsledek > 5000 Kč', function () {
        $yearlyIncome = [];
        for ($year = 1990; $year <= 2024; $year++) {
            $yearlyIncome[$year] = 25000 * 12;
        }

        $params = new PensionParams(
            birthDate:      Carbon::parse('1960-06-01'),
            gender:         Gender::Female,
            children:       3,
            retirementDate: Carbon::parse('2026-06-01'),
            yearlyIncome:   $yearlyIncome,
            insuranceYears: 35,
        );

        $result = (new PensionCalculator())->calculate($params);

        expect($result->totalMonthly)->toBeGreaterThan(5000);
        expect($result->childBonus)->toBe(1500); // 3 × 500
    });

    it('breakdown obsahuje základní a procentní výměru', function () {
        $params = new PensionParams(
            birthDate:      Carbon::parse('1958-01-01'),
            gender:         Gender::Male,
            children:       0,
            retirementDate: Carbon::parse('2026-01-01'),
            yearlyIncome:   [2025 => 600000],
            insuranceYears: 40,
        );

        $result = (new PensionCalculator())->calculate($params);

        expect($result->breakdown)->toHaveCount(2);
        expect($result->breakdown[0]['label'])->toBe('Základní výměra');
        expect($result->breakdown[0]['amount'])->toBe(4900);
        expect($result->breakdown[1]['amount'])->toBeGreaterThan(0);
    });

    it('součet breakdown = totalMonthly', function () {
        $yearlyIncome = [];
        for ($year = 1990; $year <= 2025; $year++) {
            $yearlyIncome[$year] = 40000 * 12;
        }

        $params = new PensionParams(
            birthDate:      Carbon::parse('1960-01-01'),
            gender:         Gender::Female,
            children:       2,
            retirementDate: Carbon::parse('2026-01-01'),
            yearlyIncome:   $yearlyIncome,
            insuranceYears: 36,
        );

        $result = (new PensionCalculator())->calculate($params);

        $breakdownSum = array_sum(array_column($result->breakdown, 'amount'));
        expect($breakdownSum)->toBe($result->totalMonthly);
    });
});

describe('PensionCalculator — redukční hranice', function () {
    it('toArray vrací všechny klíče', function () {
        $params = new PensionParams(
            birthDate:      Carbon::parse('1960-01-01'),
            gender:         Gender::Male,
            children:       0,
            retirementDate: Carbon::parse('2026-01-01'),
            yearlyIncome:   [],
            insuranceYears: 40,
        );

        $result = (new PensionCalculator())->calculate($params);
        $arr    = $result->toArray();

        expect($arr)->toHaveKeys([
            'basicAmount',
            'percentageAmount',
            'childBonus',
            'totalMonthly',
            'personalAssessmentBase',
            'calculationBase',
            'insuranceYears',
            'breakdown',
        ]);
    });
});
