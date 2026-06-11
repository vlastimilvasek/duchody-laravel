<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PensionStatistic;
use Illuminate\Database\Seeder;

final class PensionStatisticSeeder extends Seeder
{
    /**
     * Průměrný starobní důchod ČR podle roku (Kč/měsíc) — dle ČSSZ.
     * Orientační data pro vývoj; v produkci importovat z ČSSZ xlsx.
     *
     * @var array<int, array{avg: int, count: int}>
     */
    private const NATIONAL_BY_YEAR = [
        2010 => ['avg' => 10123, 'count' => 2260000],
        2011 => ['avg' => 10552, 'count' => 2280000],
        2012 => ['avg' => 10778, 'count' => 2310000],
        2013 => ['avg' => 10970, 'count' => 2340000],
        2014 => ['avg' => 11075, 'count' => 2355000],
        2015 => ['avg' => 11348, 'count' => 2370000],
        2016 => ['avg' => 11460, 'count' => 2390000],
        2017 => ['avg' => 11850, 'count' => 2400000],
        2018 => ['avg' => 12418, 'count' => 2410000],
        2019 => ['avg' => 13468, 'count' => 2415000],
        2020 => ['avg' => 14479, 'count' => 2410000],
        2021 => ['avg' => 15425, 'count' => 2415000],
        2022 => ['avg' => 16280, 'count' => 2370000],
        2023 => ['avg' => 19437, 'count' => 2360000],
        2024 => ['avg' => 20693, 'count' => 2365000],
        2025 => ['avg' => 21042, 'count' => 2370000],
        2026 => ['avg' => 21400, 'count' => 2375000],
    ];

    /**
     * Průměrný starobní důchod 2026 podle kraje (kód NUTS3 => Kč, počet).
     *
     * @var array<string, array{avg: int, count: int}>
     */
    private const REGIONS_2026 = [
        'CZ010' => ['avg' => 22890, 'count' => 268000],
        'CZ020' => ['avg' => 21850, 'count' => 295000],
        'CZ031' => ['avg' => 21080, 'count' => 152000],
        'CZ032' => ['avg' => 21320, 'count' => 138000],
        'CZ041' => ['avg' => 20280, 'count' => 70000],
        'CZ042' => ['avg' => 20460, 'count' => 196000],
        'CZ051' => ['avg' => 20910, 'count' => 103000],
        'CZ052' => ['avg' => 21140, 'count' => 134000],
        'CZ053' => ['avg' => 20980, 'count' => 124000],
        'CZ063' => ['avg' => 20870, 'count' => 122000],
        'CZ064' => ['avg' => 21390, 'count' => 273000],
        'CZ071' => ['avg' => 20760, 'count' => 152000],
        'CZ072' => ['avg' => 20680, 'count' => 142000],
        'CZ080' => ['avg' => 21310, 'count' => 295000],
    ];

    /** Vývoj krajů 2022–2026 odvozen poměrem k celostátnímu průměru daného roku. */
    private const REGION_HISTORY_YEARS = [2022, 2023, 2024, 2025, 2026];

    public function run(): void
    {
        $rows = [];
        $now  = now();

        // ─── Celostátní starobní důchod 2010–2026 ───
        foreach (self::NATIONAL_BY_YEAR as $year => $data) {
            $rows[] = [
                'period'         => "{$year}-01-01",
                'region_code'    => 'CZ0',
                'pension_type'   => 'starobni',
                'count'          => $data['count'],
                'average_amount' => $data['avg'],
                'source'         => 'ČSSZ',
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        // ─── Celostátní invalidní + vdovský (poslední rok) ───
        $rows[] = [
            'period' => '2026-01-01', 'region_code' => 'CZ0', 'pension_type' => 'invalidni',
            'count' => 410000, 'average_amount' => 13280, 'source' => 'ČSSZ',
            'created_at' => $now, 'updated_at' => $now,
        ];
        $rows[] = [
            'period' => '2026-01-01', 'region_code' => 'CZ0', 'pension_type' => 'vdovsky',
            'count' => 530000, 'average_amount' => 14760, 'source' => 'ČSSZ',
            'created_at' => $now, 'updated_at' => $now,
        ];

        // ─── Kraje: starobní 2022–2026 + invalidní a vdovský 2026 ───
        $national2026 = self::NATIONAL_BY_YEAR[2026]['avg'];

        foreach (self::REGIONS_2026 as $code => $data) {
            $ratio = $data['avg'] / $national2026;

            foreach (self::REGION_HISTORY_YEARS as $year) {
                $rows[] = [
                    'period'         => "{$year}-01-01",
                    'region_code'    => $code,
                    'pension_type'   => 'starobni',
                    'count'          => $data['count'],
                    'average_amount' => (int) round(self::NATIONAL_BY_YEAR[$year]['avg'] * $ratio),
                    'source'         => 'ČSSZ',
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }

            $rows[] = [
                'period' => '2026-01-01', 'region_code' => $code, 'pension_type' => 'invalidni',
                'count' => (int) round($data['count'] * 0.17),
                'average_amount' => (int) round($data['avg'] * 0.62),
                'source' => 'ČSSZ', 'created_at' => $now, 'updated_at' => $now,
            ];
            $rows[] = [
                'period' => '2026-01-01', 'region_code' => $code, 'pension_type' => 'vdovsky',
                'count' => (int) round($data['count'] * 0.22),
                'average_amount' => (int) round($data['avg'] * 0.69),
                'source' => 'ČSSZ', 'created_at' => $now, 'updated_at' => $now,
            ];
        }

        PensionStatistic::query()->delete();
        PensionStatistic::insert($rows);
    }
}
