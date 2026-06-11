<?php

declare(strict_types=1);

namespace App\Services\Pension\DTO;

final readonly class PensionResult
{
    /**
     * @param array<int, array{label: string, amount: int}> $breakdown
     */
    public function __construct(
        public int $basicAmount,
        public int $percentageAmount,
        public int $childBonus,
        public int $totalMonthly,
        public int $personalAssessmentBase,
        public int $calculationBase,
        public int $insuranceYears,
        public array $breakdown,
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'basicAmount'            => $this->basicAmount,
            'percentageAmount'       => $this->percentageAmount,
            'childBonus'             => $this->childBonus,
            'totalMonthly'           => $this->totalMonthly,
            'personalAssessmentBase' => $this->personalAssessmentBase,
            'calculationBase'        => $this->calculationBase,
            'insuranceYears'         => $this->insuranceYears,
            'breakdown'              => $this->breakdown,
        ];
    }
}
