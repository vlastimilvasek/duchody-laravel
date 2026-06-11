<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PensionParameterSeeder::class,
            WageCoefficientSeeder::class,
            PensionFundSeeder::class,
            PensionStatisticSeeder::class,
            ArticleSeeder::class,
        ]);
    }
}
