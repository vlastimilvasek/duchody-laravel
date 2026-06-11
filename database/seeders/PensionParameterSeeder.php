<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PensionParameter;
use Illuminate\Database\Seeder;

class PensionParameterSeeder extends Seeder
{
    public function run(): void
    {
        PensionParameter::upsert([
            [
                'year'                     => 2026,
                'basic_amount'             => 4900,
                'average_wage'             => 48833,
                'reduction_boundary_1'     => 16306,
                'reduction_boundary_2'     => 48833,
                'reduction_boundary_3'     => 130221,
                'percentage_rate_per_year' => 1.4950,
                'reduction_rate_1'         => 0.9900,
                'reduction_rate_2'         => 0.2600,
                'reduction_rate_3'         => 0.2200,
                'child_bonus'              => 500,
                'min_percentage_amount'    => 770,
            ],
            [
                'year'                     => 2025,
                'basic_amount'             => 4660,
                'average_wage'             => 46557,
                'reduction_boundary_1'     => 15527,
                'reduction_boundary_2'     => 46557,
                'reduction_boundary_3'     => 124152,
                'percentage_rate_per_year' => 1.5000,
                'reduction_rate_1'         => 1.0000,
                'reduction_rate_2'         => 0.2600,
                'reduction_rate_3'         => 0.2200,
                'child_bonus'              => 500,
                'min_percentage_amount'    => 770,
            ],
        ], ['year'], [
            'basic_amount',
            'average_wage',
            'reduction_boundary_1',
            'reduction_boundary_2',
            'reduction_boundary_3',
            'percentage_rate_per_year',
            'reduction_rate_1',
            'reduction_rate_2',
            'reduction_rate_3',
            'child_bonus',
            'min_percentage_amount',
        ]);
    }
}
