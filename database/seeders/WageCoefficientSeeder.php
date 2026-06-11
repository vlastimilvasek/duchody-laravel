<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Services\Pension\Data\WageCoefficients;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WageCoefficientSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [];

        foreach (WageCoefficients::FOR_2026 as $incomeYear => $coefficient) {
            $rows[] = [
                'reference_year' => 2026,
                'income_year'    => $incomeYear,
                'coefficient'    => $coefficient,
            ];
        }

        DB::table('wage_coefficients')->upsert(
            $rows,
            ['reference_year', 'income_year'],
            ['coefficient'],
        );
    }
}
