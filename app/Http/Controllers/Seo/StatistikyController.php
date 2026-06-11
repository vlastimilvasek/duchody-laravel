<?php

declare(strict_types=1);

namespace App\Http\Controllers\Seo;

use App\Http\Controllers\Controller;
use App\Models\PensionStatistic;
use Illuminate\View\View;

final class StatistikyController extends Controller
{
    /** Náhradový poměr důchodu k průměrné mzdě (%, OECD/Eurostat — orientační). */
    private const EU_COMPARISON = [
        ['country' => 'Rakousko', 'value' => 74],
        ['country' => 'Itálie', 'value' => 70],
        ['country' => 'EU průměr', 'value' => 58],
        ['country' => 'Německo', 'value' => 53],
        ['country' => 'Slovensko', 'value' => 50],
        ['country' => 'Česko', 'value' => 44],
        ['country' => 'Polsko', 'value' => 40],
    ];

    /** Rozložení výše starobních důchodů (pásma, % důchodců — ČSSZ, orientační). */
    private const DISTRIBUTION = [
        ['range' => 'do 14 000', 'share' => 4],
        ['range' => '14–17 000', 'share' => 12],
        ['range' => '17–20 000', 'share' => 26],
        ['range' => '20–23 000', 'share' => 31],
        ['range' => '23–26 000', 'share' => 17],
        ['range' => '26–30 000', 'share' => 7],
        ['range' => 'nad 30 000', 'share' => 3],
    ];

    public function index(): View
    {
        $national = PensionStatistic::where('region_code', 'CZ0')
            ->where('pension_type', 'starobni')
            ->orderBy('period')
            ->get();

        $latest = $national->last();

        $invalidni = PensionStatistic::where('region_code', 'CZ0')
            ->where('pension_type', 'invalidni')
            ->orderByDesc('period')
            ->first();

        $vdovsky = PensionStatistic::where('region_code', 'CZ0')
            ->where('pension_type', 'vdovsky')
            ->orderByDesc('period')
            ->first();

        // Kraje pro srovnávací grid (poslední období)
        $regions       = collect(config('regions'));
        $latestPeriod  = $latest?->period;
        $regionalStats = PensionStatistic::whereIn('region_code', $regions->pluck('code'))
            ->where('pension_type', 'starobni')
            ->when($latestPeriod, fn ($q) => $q->whereDate('period', $latestPeriod))
            ->get()
            ->keyBy('region_code');

        $regionCards = $regions->map(fn (array $region): array => [
            'slug'    => $region['slug'],
            'name'    => $region['name'],
            'average' => $regionalStats[$region['code']]->average_amount ?? null,
            'diff'    => isset($regionalStats[$region['code']]) && $latest !== null
                ? $regionalStats[$region['code']]->average_amount - $latest->average_amount
                : null,
        ])->sortByDesc('average')->values()->all();

        $dashboard = [
            'years'        => $national->map(fn (PensionStatistic $s) => $s->period->year)->all(),
            'averages'     => $national->pluck('average_amount')->all(),
            'counts'       => $national->pluck('count')->all(),
            'distribution' => self::DISTRIBUTION,
            'euComparison' => self::EU_COMPARISON,
        ];

        $meta = [
            'title'       => 'Statistiky důchodů 2026 — Průměrný důchod, vývoj a srovnání | Důchody.cz',
            'description' => 'Průměrný důchod v ČR, vývoj od roku 2010, rozložení výše důchodů, srovnání s EU a data za všechny kraje.',
            'canonical'   => route('statistiky.index'),
        ];

        return view('statistiky.index', compact('latest', 'invalidni', 'vdovsky', 'regionCards', 'dashboard', 'meta'));
    }

    public function show(string $kraj): View
    {
        $region = collect(config('regions'))->firstWhere('slug', $kraj);
        abort_if($region === null, 404);

        $starobni = PensionStatistic::where('region_code', $region['code'])
            ->where('pension_type', 'starobni')
            ->orderBy('period')
            ->get();

        abort_if($starobni->isEmpty(), 404);

        $latest = $starobni->last();

        $invalidni = PensionStatistic::where('region_code', $region['code'])
            ->where('pension_type', 'invalidni')
            ->orderByDesc('period')
            ->first();

        $vdovsky = PensionStatistic::where('region_code', $region['code'])
            ->where('pension_type', 'vdovsky')
            ->orderByDesc('period')
            ->first();

        // Celostátní průměr ke stejnému období — pro srovnání
        $nationalLatest = PensionStatistic::where('region_code', 'CZ0')
            ->where('pension_type', 'starobni')
            ->whereDate('period', $latest->period)
            ->first();

        $comparison = null;
        if ($nationalLatest !== null) {
            $diff       = $latest->average_amount - $nationalLatest->average_amount;
            $comparison = [
                'national' => $nationalLatest->average_amount,
                'diffKc'   => $diff,
                'diffPct'  => round($diff / $nationalLatest->average_amount * 100, 1),
            ];
        }

        $trend = [
            'years'    => $starobni->map(fn (PensionStatistic $s) => $s->period->year)->all(),
            'averages' => $starobni->pluck('average_amount')->all(),
        ];

        $otherRegions = collect(config('regions'))
            ->where('slug', '!=', $kraj)
            ->values()
            ->all();

        $meta = [
            'title'       => "Průměrný důchod — {$region['name']} 2026 | Důchody.cz",
            'description' => "Průměrná výše starobního, invalidního a vdovského důchodu: {$region['name']}. Srovnání s celostátním průměrem a vývoj za 5 let.",
            'canonical'   => route('statistiky.show', $kraj),
        ];

        return view('statistiky.show', [
            'region'     => $region,
            'latest'     => $latest,
            'invalidni'  => $invalidni,
            'vdovsky'    => $vdovsky,
            'comparison' => $comparison,
            'trend'      => $trend,
            'otherRegions' => $otherRegions,
            'meta'       => $meta,
        ]);
    }
}
