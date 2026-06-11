<?php

declare(strict_types=1);

namespace App\Services\Pension;

use App\Services\Pension\Data\Parameters;
use App\Services\Pension\Data\WageCoefficients;
use App\Services\Pension\DTO\PensionParams;
use App\Services\Pension\DTO\PensionResult;

final class PensionCalculator
{
    /**
     * Rozhodné období = 1986 až rok před přiznáním důchodu.
     *
     * @return array{start: int, end: int}
     */
    private function decisionPeriod(int $retirementYear): array
    {
        return ['start' => 1986, 'end' => $retirementYear - 1];
    }

    /**
     * Roční vyměřovací základ = hrubý příjem × koeficient nárůstu.
     * Zastropován na 48× průměrná měsíční mzda × 12.
     */
    /**
     * @param array<int, float> $coefficients
     */
    private function annualAssessmentBase(
        float $grossIncome,
        int $year,
        array $coefficients,
        float $maxAnnualBase,
    ): float {
        $adjusted = $grossIncome * ($coefficients[$year] ?? 1.0);

        return min($adjusted, $maxAnnualBase);
    }

    /**
     * Osobní vyměřovací základ (OVZ):
     *   (součet ročních VZ) / (dny rozhodného období − vyloučené dny) × 30.4167
     *
     * @param array<int, float> $annualBases
     */
    private function ovz(array $annualBases, float $totalInsuredDays, float $excludedDays): float
    {
        $sumBases     = array_sum($annualBases);
        $effectiveDays = $totalInsuredDays - $excludedDays;

        if ($effectiveDays <= 0.0) {
            return 0.0;
        }

        return ($sumBases / $effectiveDays) * 30.4167;
    }

    /**
     * Výpočtový základ = redukce OVZ přes redukční hranice.
     *
     * @param array<string, mixed> $params
     */
    private function calculationBase(float $ovz, array $params): float
    {
        $rb1 = (float) $params['reductionBoundary1'];
        $rb2 = (float) $params['reductionBoundary2'];
        $rb3 = (float) $params['reductionBoundary3'];
        $rr1 = (float) $params['reductionRate1'];
        $rr2 = (float) $params['reductionRate2'];
        $rr3 = (float) $params['reductionRate3'];

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

        return $base; // nad 3. hranicí 0 %
    }

    /**
     * Procentní výměra:
     *   výpočtový základ × roky pojištění × sazba %
     *
     * Přesluhování: +1.5 % výpočtového základu za každých 90 dní (earlyDays < 0).
     * Předčasnost: −1.5 % za každých i započatých 90 dní (earlyDays > 0),
     *              při 45+ letech pojištění pouze −0.75 %.
     */
    private function percentageAmount(
        float $calculationBase,
        int $insuranceYears,
        int $earlyDays,
        float $rate,
        float $minAmount,
    ): float {
        $amount = $calculationBase * ($rate / 100.0) * $insuranceYears;

        if ($earlyDays > 0) {
            // Předčasný odchod
            $periods           = (int) ceil($earlyDays / 90);
            $reductionPerPeriod = $insuranceYears >= 45 ? 0.0075 : 0.015;
            $amount            -= $calculationBase * $reductionPerPeriod * $periods;
        } elseif ($earlyDays < 0) {
            // Přesluhování — každých 90 dní +1.5 %
            $periods = (int) floor(abs($earlyDays) / 90);
            $amount += $calculationBase * 0.015 * $periods;
        }

        return max($minAmount, $amount);
    }

    /**
     * Počet dní předčasnosti (kladné = předčasný, záporné = přesluhování).
     */
    private function earlyDays(PensionParams $params): int
    {
        $normalRetirement = RetirementAge::retirementDate(
            $params->birthDate,
            $params->gender,
            $params->children,
        );

        return (int) $params->retirementDate->diffInDays($normalRetirement, false);
    }

    /**
     * Hlavní výpočet starobního důchodu.
     */
    public function calculate(PensionParams $params): PensionResult
    {
        $rp           = Parameters::forYear($params->retirementDate->year);
        $coefficients = WageCoefficients::forYear($params->retirementDate->year);

        // Krok 1: Rozhodné období
        ['start' => $start, 'end' => $end] = $this->decisionPeriod($params->retirementDate->year);

        // Krok 2: Roční vyměřovací základy
        $maxAnnualBase = (float) $rp['averageWage'] * $rp['maxAssessmentMultiplier'];
        $annualBases   = [];

        for ($year = $start; $year <= $end; $year++) {
            $income = (float) ($params->yearlyIncome[$year] ?? 0);
            if ($income > 0.0) {
                $annualBases[$year] = $this->annualAssessmentBase(
                    $income,
                    $year,
                    $coefficients,
                    $maxAnnualBase,
                );
            }
        }

        // Krok 3: OVZ
        $totalInsuredDays = (float) $params->insuranceYears * 365.25;
        $ovz              = $this->ovz($annualBases, $totalInsuredDays, $params->excludedDays);

        // Krok 4: Výpočtový základ
        $calcBase = $this->calculationBase($ovz, $rp);

        // Krok 5: Procentní výměra (s korekcí za předčasnost/přesluhování)
        $earlyDays        = $this->earlyDays($params);
        $percentageAmount = $this->percentageAmount(
            $calcBase,
            $params->insuranceYears,
            $earlyDays,
            (float) $rp['percentageRatePerYear'],
            (float) $rp['minPercentageAmount'],
        );

        // Výchovné
        $childBonus = $params->children * (int) $rp['childBonus'];

        // Celkem
        $total = (int) $rp['basicAmount'] + $percentageAmount + $childBonus;

        // Breakdown pro vizualizaci
        $breakdown = [
            ['label' => 'Základní výměra', 'amount' => (int) $rp['basicAmount']],
            [
                'label'  => "Procentní výměra ({$params->insuranceYears} let × {$rp['percentageRatePerYear']} %)",
                'amount' => (int) round($percentageAmount),
            ],
        ];

        if ($childBonus > 0) {
            $breakdown[] = [
                'label'  => "Výchovné ({$params->children} " . ($params->children === 1 ? 'dítě' : 'děti') . ')',
                'amount' => $childBonus,
            ];
        }

        return new PensionResult(
            basicAmount:            (int) $rp['basicAmount'],
            percentageAmount:       (int) round($percentageAmount),
            childBonus:             $childBonus,
            totalMonthly:           (int) round($total),
            personalAssessmentBase: (int) round($ovz),
            calculationBase:        (int) round($calcBase),
            insuranceYears:         $params->insuranceYears,
            breakdown:              $breakdown,
        );
    }
}
