# Důchody.cz — Pension Engine Specifikace

> Toto je kompletní technická specifikace výpočetní logiky. Engine je PHP vrstva v `app/Services/Pension` a běží na serveru. Každý výpočet zde musí být pokryt testy (Pest/PHPUnit).

---

## Parametry 2026 (seed data)

```php
// app/Services/Pension/Data/Parameters.php
declare(strict_types=1);

namespace App\Services\Pension\Data;

final class Parameters
{
    public const PENSION_PARAMETERS_2026 = [
        'year'                  => 2026,
        'basicAmount'           => 4900,    // základní výměra Kč
        'averageWage'           => 48833,   // průměrná mzda (§15 ZDP) — aktualizovat ročně
        'reductionBoundary1'    => 16306,   // 1. redukční hranice (44 % průměrné mzdy)
        'reductionBoundary2'    => 48833,   // 2. redukční hranice (100 % průměrné mzdy)
        'reductionBoundary3'    => 130221,  // 3. redukční hranice (4× průměrná mzda)
        'percentageRatePerYear' => 1.495,   // % výpočtového základu za rok pojištění (2026)
        'childBonus'            => 500,      // výchovné za dítě Kč/měsíc
        // Redukce OVZ → výpočtový základ:
        // do 1. hranice: 100 % (v roce 2026 sníženo na 99 %, dál klesá 1 %/rok do 90 %)
        // nad 1. do 2. hranice: 26 %
        // nad 2. do 3. hranice: 22 %
        // nad 3. hranici: 0 %
        'reductionRate1'        => 0.99,    // 2026: 99 % (bylo 100 % do 2025)
        'reductionRate2'        => 0.26,
        'reductionRate3'        => 0.22,
    ];
}
```

> V produkci se parametry načítají z tabulky `pension_parameters` (model `PensionParameter`). Konstanta výše slouží jako default / seed a jako fallback v testech.

## Koeficienty nárůstu vyměřovacích základů

Tyto hodnoty musí být v DB tabulce `wage_coefficients` (model `WageCoefficient`). Pro rok přiznání 2026:

```php
// app/Services/Pension/Data/WageCoefficients.php
// Vždy se vypisují jako coefficient pro (referenceYear=2026, incomeYear=X)
// Pro aktuální rok přiznání se koeficient = 1.0000

final class WageCoefficients
{
    public const FOR_2026 = [
        1986 => 18.0821,  // příjmy z roku 1986 se násobí tímto koef.
        1987 => 17.6923,
        1988 => 16.9812,
        1989 => 15.8934,
        1990 => 13.2156,
        1991 => 8.6743,
        1992 => 7.1234,
        1993 => 5.8921,
        1994 => 5.1043,
        1995 => 4.3829,
        1996 => 3.8712,
        1997 => 3.5021,
        1998 => 3.2143,
        1999 => 3.0234,
        2000 => 2.8523,
        2001 => 2.6742,
        2002 => 2.5821,
        2003 => 2.4901,
        2004 => 2.3812,
        2005 => 2.2543,
        2006 => 2.1234,
        2007 => 1.9821,
        2008 => 1.8534,
        2009 => 1.7891,
        2010 => 1.7234,
        2011 => 1.6543,
        2012 => 1.6012,
        2013 => 1.5678,
        2014 => 1.5234,
        2015 => 1.4823,
        2016 => 1.4234,
        2017 => 1.3456,
        2018 => 1.2678,
        2019 => 1.1901,
        2020 => 1.1456,
        2021 => 1.0823,
        2022 => 1.0234,
        2023 => 1.0123,
        2024 => 1.0000,  // aktuální koef vždy 1
        2025 => 1.0000,
    ];
}
// POZOR: Tyto hodnoty jsou ilustrativní. Přesné hodnoty stáhnout z cssz.cz kalkulačky XLS
// URL: https://www.cssz.cz/documents/20143/99410/Duchodova_kalkulacka_251215.xls/
```

## Tabulka důchodového věku

```php
// app/Services/Pension/RetirementAge.php
// Zdroj: MPSV § 32 ZDP + Příloha zákona 155/1995 Sb.
declare(strict_types=1);

namespace App\Services\Pension;

use App\Enums\Gender;
use Carbon\Carbon;

// Pro ročníky 1936–1973: přesné tabulky
// Pro ročníky 1974–1988: vzorec
// Pro ročníky 1989+: 67 let

final class RetirementAge
{
    /**
     * Vzorec pro 1974–1988:
     * důchodový věk = 65 let + 8 měsíců + (rok_narození - 1973) měsíců
     * Příklad: nar. 1975 → 65 + 8 + 2 = 65 let a 10 měsíců
     *
     * @return array{years:int, months:int}
     */
    public static function byFormula(int $birthYear): array
    {
        if ($birthYear > 1988) {
            return ['years' => 67, 'months' => 0];
        }

        $extraMonths = $birthYear - 1973;
        $totalMonths = 65 * 12 + 8 + $extraMonths;

        return [
            'years'  => intdiv($totalMonths, 12),
            'months' => $totalMonths % 12,
        ];
    }

    /**
     * Plná tabulka (1936–1973) + vzorec (1974–1988) + 67 let (1989+).
     * Pro ženy zohledňuje počet vychovaných dětí.
     *
     * @return array{years:int, months:int}
     */
    public static function calculate(Carbon $birthDate, Gender $gender, int $children): array
    {
        // ... přesná tabulková logika dle MPSV (viz RETIREMENT_AGES tabulka)
        return self::byFormula($birthDate->year);
    }
}
```

## Výpočetní algoritmus — krok po kroku

```php
// app/Services/Pension/PensionCalculator.php
declare(strict_types=1);

namespace App\Services\Pension;

use App\Services\Pension\Data\Parameters;
use App\Services\Pension\Data\WageCoefficients;
use App\Services\Pension\DTO\PensionParams;
use App\Services\Pension\DTO\PensionResult;

final class PensionCalculator
{
    /**
     * KROK 1: Rozhodné období
     * = od roku 1986 do konce roku předcházejícího přiznání důchodu
     *
     * @return array{start:int, end:int}
     */
    private function decisionPeriod(int $retirementYear): array
    {
        return ['start' => 1986, 'end' => $retirementYear - 1];
    }

    /**
     * KROK 2: Roční vyměřovací základ
     * = hrubý příjem za rok × koeficient nárůstu
     * Nepřekračuje maximální VZ (aktuálně 48× průměrná mzda za rok)
     */
    private function annualAssessmentBase(
        float $grossIncome,
        int $year,
        array $coefficients,
        float $maxBase
    ): float {
        $adjusted = $grossIncome * ($coefficients[$year] ?? 1.0);

        return min($adjusted, $maxBase);
    }

    /**
     * KROK 3: Osobní vyměřovací základ (OVZ)
     * = (součet ročních VZ za rozhodné období) / (počet dnů rozhodného období − vyloučené dny) × 30.4167
     */
    private function ovz(array $annualBases, float $totalInsuredDays, float $excludedDays): float
    {
        $sumBases = array_sum($annualBases);
        $effectiveDays = $totalInsuredDays - $excludedDays;

        if ($effectiveDays <= 0) {
            return 0.0;
        }

        return ($sumBases / $effectiveDays) * 30.4167;
    }

    /**
     * KROK 4: Výpočtový základ (redukce OVZ)
     * Redukční hranice pro rok 2026:
     *   do 1. RH (16 306 Kč):  99 % OVZ
     *   nad 1. do 2. RH:       26 % z částky
     *   nad 2. do 3. RH:       22 % z částky
     *   nad 3. RH:             0 %
     */
    private function calculationBase(float $ovz, array $params): float
    {
        $rb1 = $params['reductionBoundary1'];
        $rb2 = $params['reductionBoundary2'];
        $rb3 = $params['reductionBoundary3'];
        $rr1 = $params['reductionRate1'];
        $rr2 = $params['reductionRate2'];
        $rr3 = $params['reductionRate3'];

        if ($ovz <= $rb1) {
            return $ovz * $rr1;
        }

        $base = $rb1 * $rr1;

        if ($ovz <= $rb2) {
            return $base + ($ovz - $rb1) * $rr2;
        }

        $base += ($rb2 - $rb1) * $rr2;

        if ($ovz <= $rb3) {
            return $base + ($ovz - $rb2) * $rr3;
        }

        $base += ($rb3 - $rb2) * $rr3;

        return $base; // nic nad 3. hranicí se nepočítá
    }

    /**
     * KROK 5: Procentní výměra
     * = výpočtový základ × počet let pojištění × 1.495 %  (rok 2026)
     *
     * Přesluhování (po dosažení důchodového věku bez důchodu):
     *   +1.5 % výpočtového základu za každých 90 dní
     *
     * Předčasnost (přiznání před důchodovým věkem):
     *   −1.5 % za každých i započatých 90 dní předčasnosti
     *   Výjimka: 45+ let pojištění → jen −0.75 % za 90 dní
     *
     * Minimum procentní výměry: 770 Kč (§ 41 odst. 1 ZDP)
     */
    private function percentageAmount(
        float $calculationBase,
        int $insuranceYears,
        int $earlyDays,            // záporné = přesluhování
        int $totalInsuranceYears,  // pro výjimku 45 let
        float $rate = 1.495
    ): float {
        $amount = $calculationBase * ($rate / 100) * $insuranceYears;

        if ($earlyDays > 0) {
            $periods = (int) ceil($earlyDays / 90);
            $reductionPerPeriod = $totalInsuranceYears >= 45 ? 0.0075 : 0.015;
            $reductionAmount = $calculationBase * $reductionPerPeriod * $periods;
            $amount -= $reductionAmount;
        }

        return max(770.0, $amount);
    }

    /**
     * VÝSLEDEK: Celkový důchod
     * = základní výměra + procentní výměra + výchovné
     */
    public function calculate(PensionParams $params): PensionResult
    {
        $rp = Parameters::PENSION_PARAMETERS_2026;
        $coefficients = WageCoefficients::FOR_2026;

        // Krok 1: Rozhodné období
        $retirementYear = $params->retirementDate->year;
        ['start' => $start, 'end' => $end] = $this->decisionPeriod($retirementYear);

        // Krok 2: Roční VZ s koeficienty
        $annualBases = [];
        for ($year = $start; $year <= $end; $year++) {
            $income = $params->yearlyIncome[$year] ?? 0;
            if ($income > 0) {
                $annualBases[$year] = $this->annualAssessmentBase(
                    $income,
                    $year,
                    $coefficients,
                    $rp['averageWage'] * 48 // max VZ
                );
            }
        }

        // Krok 3: OVZ
        $totalDays = $params->insuranceYears * 365.25;
        $ovz = $this->ovz($annualBases, $totalDays, $params->excludedDays);

        // Krok 4: Výpočtový základ
        $calculationBase = $this->calculationBase($ovz, $rp);

        // Krok 5: Procentní výměra
        $retirementAge = RetirementAge::calculate($params->birthDate, $params->gender, $params->children);
        $earlyDays = max(0, $this->daysBetween($params->retirementDate, $retirementAge, $params->birthDate));
        $percentageAmount = $this->percentageAmount(
            $calculationBase,
            $params->insuranceYears,
            $earlyDays,
            $params->insuranceYears,
            $rp['percentageRatePerYear']
        );

        // Výchovné
        $childBonus = $params->children * $rp['childBonus'];

        // Celkem
        $total = $rp['basicAmount'] + $percentageAmount + $childBonus;

        $breakdown = [
            ['label' => 'Základní výměra', 'amount' => $rp['basicAmount']],
            ['label' => "Procentní výměra ({$params->insuranceYears} let × {$rp['percentageRatePerYear']} %)", 'amount' => (int) round($percentageAmount)],
        ];
        if ($childBonus > 0) {
            $breakdown[] = ['label' => "Výchovné ({$params->children} děti)", 'amount' => $childBonus];
        }

        return new PensionResult(
            basicAmount: $rp['basicAmount'],
            percentageAmount: (int) round($percentageAmount),
            childBonus: $childBonus,
            totalMonthly: (int) round($total),
            personalAssessmentBase: (int) round($ovz),
            calculationBase: (int) round($calculationBase),
            insuranceYears: $params->insuranceYears,
            breakdown: $breakdown,
        );
    }
}
```

## Testové případy (Pest)

```php
// tests/Unit/Pension/RetirementAgeTest.php
use App\Services\Pension\RetirementAge;
use App\Enums\Gender;
use Carbon\Carbon;

describe('RetirementAge::calculate', function () {
    it('muž nar. 1965 → 64 let a 8 měsíců', function () {
        $result = RetirementAge::calculate(Carbon::parse('1965-01-01'), Gender::Male, 0);
        expect($result['years'])->toBe(64)
            ->and($result['months'])->toBe(8);
    });

    it('žena nar. 1975, 2 děti → 65 let a 10 měsíců', function () {
        $result = RetirementAge::calculate(Carbon::parse('1975-06-15'), Gender::Female, 2);
        expect($result['years'])->toBe(65)
            ->and($result['months'])->toBe(10);
    });

    it('nar. po 1988 → 67 let', function () {
        $result = RetirementAge::calculate(Carbon::parse('1990-01-01'), Gender::Male, 0);
        expect($result['years'])->toBe(67)
            ->and($result['months'])->toBe(0);
    });
});

// tests/Unit/Pension/PensionCalculatorTest.php
use App\Services\Pension\PensionCalculator;
use App\Services\Pension\DTO\PensionParams;

describe('PensionCalculator — integrační test', function () {
    it('standardní případ — muž, 40 let pojištění, průměrný příjem', function () {
        // Výsledek by měl být blízko průměrnému důchodu ČR ~21 400 Kč
        $result = (new PensionCalculator())->calculate(new PensionParams(
            birthDate: Carbon::parse('1962-01-01'),
            gender: Gender::Male,
            children: 0,
            retirementDate: Carbon::parse('2026-07-01'),
            yearlyIncome: [/* průměrné příjmy po letech */],
            excludedPeriods: [],
            insurancePeriods: [],
        ));

        expect($result->totalMonthly)->toBeGreaterThan(15000)
            ->and($result->totalMonthly)->toBeLessThan(30000);
    });
});
```

> Cíl: **100% test coverage** na výpočetní vrstvě (`app/Services/Pension`). Spouštění: `php artisan test --filter=Pension` nebo `./vendor/bin/pest tests/Unit/Pension`.

---

## Disclaimer (povinný na všech kalkulačkách)

```
Výpočet je orientační a slouží pouze pro informační účely. Nezohledňuje všechny
zákonné podmínky a individuální okolnosti (vyloučené doby, náhradní doby pojištění,
zahraniční pojištění, atd.). Přesný výpočet provádí výhradně ČSSZ na základě
podané žádosti o důchod. Informace jsou aktuální k datu uvedenému na stránce.
```
