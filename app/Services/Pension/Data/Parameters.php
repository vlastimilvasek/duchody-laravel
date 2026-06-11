<?php

declare(strict_types=1);

namespace App\Services\Pension\Data;

final class Parameters
{
    /**
     * Parametry pro rok 2026.
     * Zdroj: ČSSZ, MPSV — aktualizovat každý leden.
     *
     * @var array<string, mixed>
     */
    public const PENSION_PARAMETERS_2026 = [
        'year'                  => 2026,
        'basicAmount'           => 4900,
        'averageWage'           => 48833,
        'reductionBoundary1'    => 16306,
        'reductionBoundary2'    => 48833,
        'reductionBoundary3'    => 130221,
        'percentageRatePerYear' => 1.495,
        'childBonus'            => 500,
        // Redukce OVZ → výpočtový základ (2026: 99 %, klesá 1%/rok do 90 %)
        'reductionRate1'        => 0.99,
        'reductionRate2'        => 0.26,
        'reductionRate3'        => 0.22,
        // Minimální procentní výměra (§41 odst. 1 ZDP)
        'minPercentageAmount'   => 770,
        // Max vyměřovací základ = 48× průměrná mzda ročně
        'maxAssessmentMultiplier' => 48,
    ];

    /** @return array<string, mixed> */
    public static function forYear(int $year): array
    {
        // V produkci načíst z DB: PensionParameter::findOrFail($year)->toArray()
        return match ($year) {
            2026 => self::PENSION_PARAMETERS_2026,
            default => self::PENSION_PARAMETERS_2026, // fallback na aktuální
        };
    }
}
