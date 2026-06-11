<?php

declare(strict_types=1);

use App\Enums\Gender;
use App\Services\Pension\RetirementAge;
use Carbon\Carbon;

describe('RetirementAge::calculate — muži', function () {
    it('nar. 1940 → 62 let 0 měsíců', function () {
        $result = RetirementAge::calculate(Carbon::parse('1940-06-01'), Gender::Male, 0);
        expect($result['years'])->toBe(62)
            ->and($result['months'])->toBe(0);
    });

    it('nar. 1951 → 65 let 0 měsíců', function () {
        $result = RetirementAge::calculate(Carbon::parse('1951-01-01'), Gender::Male, 0);
        expect($result['years'])->toBe(65)
            ->and($result['months'])->toBe(0);
    });

    it('nar. 1962 → 66 let 2 měsíce', function () {
        $result = RetirementAge::calculate(Carbon::parse('1962-01-01'), Gender::Male, 0);
        expect($result['years'])->toBe(66)
            ->and($result['months'])->toBe(2);
    });

    it('nar. 1974 → 65 let 9 měsíců (vzorec 65+8+1)', function () {
        $result = RetirementAge::calculate(Carbon::parse('1974-01-01'), Gender::Male, 0);
        expect($result['years'])->toBe(65)
            ->and($result['months'])->toBe(9);
    });

    it('nar. 1975 → 65 let 10 měsíců', function () {
        $result = RetirementAge::calculate(Carbon::parse('1975-01-01'), Gender::Male, 0);
        expect($result['years'])->toBe(65)
            ->and($result['months'])->toBe(10);
    });

    it('nar. po 1988 → 67 let 0 měsíců', function () {
        $result = RetirementAge::calculate(Carbon::parse('1990-06-15'), Gender::Male, 0);
        expect($result['years'])->toBe(67)
            ->and($result['months'])->toBe(0);
    });
});

describe('RetirementAge::calculate — ženy', function () {
    it('žena nar. 1960 bez dětí → 61 let 0 měsíců', function () {
        $result = RetirementAge::calculate(Carbon::parse('1960-01-01'), Gender::Female, 0);
        expect($result['years'])->toBe(61)
            ->and($result['months'])->toBe(0);
    });

    it('žena nar. 1960, 2 děti → 60 let 4 měsíce', function () {
        $result = RetirementAge::calculate(Carbon::parse('1960-01-01'), Gender::Female, 2);
        expect($result['years'])->toBe(60)
            ->and($result['months'])->toBe(4);
    });

    it('žena nar. 1975, 2 děti → věk dle vzorce bez redukce', function () {
        $result = RetirementAge::calculate(Carbon::parse('1975-06-15'), Gender::Female, 2);
        expect($result['years'])->toBe(65)
            ->and($result['months'])->toBe(10);
    });

    it('žena nar. po 1988 → 67 let', function () {
        $result = RetirementAge::calculate(Carbon::parse('1992-01-01'), Gender::Female, 0);
        expect($result['years'])->toBe(67)
            ->and($result['months'])->toBe(0);
    });
});

describe('RetirementAge::byFormula', function () {
    it('nar. 1974 → 65 let 9 měsíců', function () {
        $result = RetirementAge::byFormula(1974);
        expect($result['years'])->toBe(65)
            ->and($result['months'])->toBe(9);
    });

    it('nar. 1985 → 66 let 8 měsíců', function () {
        $result = RetirementAge::byFormula(1985);
        expect($result['years'])->toBe(66)
            ->and($result['months'])->toBe(8);
    });

    it('nar. 1988 → 66 let 11 měsíců', function () {
        $result = RetirementAge::byFormula(1988);
        expect($result['years'])->toBe(66)
            ->and($result['months'])->toBe(11);
    });

    it('nar. po 1988 → 67 let', function () {
        $result = RetirementAge::byFormula(1995);
        expect($result['years'])->toBe(67)
            ->and($result['months'])->toBe(0);
    });
});

describe('RetirementAge::retirementDate', function () {
    it('muž nar. 1951-03-15 → odchod 2016-03-15', function () {
        $birth    = Carbon::parse('1951-03-15');
        $expected = Carbon::parse('2016-03-15');
        $result   = RetirementAge::retirementDate($birth, Gender::Male, 0);
        expect($result->isSameDay($expected))->toBeTrue();
    });
});
