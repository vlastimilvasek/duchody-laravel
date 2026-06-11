<?php

declare(strict_types=1);

namespace App\Services\Pension\DTO;

use App\Enums\Gender;
use Carbon\Carbon;

final readonly class PensionParams
{
    /**
     * @param array<int, float> $yearlyIncome     rok => hrubý roční příjem v Kč
     * @param int               $insuranceYears   celkový počet let pojištění
     * @param float             $excludedDays     vyloučené dny (mateřská, nemoc apod.)
     */
    public function __construct(
        public Carbon $birthDate,
        public Gender $gender,
        public int $children,
        public Carbon $retirementDate,
        public array $yearlyIncome,
        public int $insuranceYears,
        public float $excludedDays = 0.0,
    ) {}
}
