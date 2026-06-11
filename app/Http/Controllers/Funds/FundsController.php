<?php

declare(strict_types=1);

namespace App\Http\Controllers\Funds;

use App\Http\Controllers\Controller;
use App\Models\PensionFund;
use Illuminate\View\View;

final class FundsController extends Controller
{
    /**
     * Průměrná roční inflace ČR (CPI, ČSÚ) — pro srovnání výnosů.
     *
     * @var array<int, float>
     */
    private const INFLATION_BY_YEAR = [
        2021 => 3.8,
        2022 => 15.1,
        2023 => 10.7,
        2024 => 2.4,
        2025 => 2.6,
    ];

    public function index(): View
    {
        $funds = PensionFund::orderByDesc('return_1y')->get();

        $bestReturnSlug = $funds->sortByDesc('return_5y')->first()?->slug;
        $lowestFeeSlug  = $funds->sortBy(fn (PensionFund $f) => [$f->fee_management, $f->fee_performance])->first()?->slug;

        $fundsForTable = $funds->map(fn (PensionFund $fund): array => [
            'bestReturn'   => $fund->slug === $bestReturnSlug,
            'lowestFee'    => $fund->slug === $lowestFeeSlug,
            'slug'         => $fund->slug,
            'name'         => $fund->name,
            'company'      => $fund->company,
            'fundType'     => $fund->fund_type,
            'feeManagement' => $fund->fee_management,
            'feePerformance' => $fund->fee_performance,
            'return1y'     => $fund->return_1y,
            'return3y'     => $fund->return_3y,
            'return5y'     => $fund->return_5y,
            'assetsMil'    => $fund->total_assets_mil,
            'participants' => $fund->participants_count,
            'detailUrl'    => route('fondy.show', $fund->slug),
        ])->values()->all();

        $meta = [
            'title'       => 'Srovnání penzijních fondů 2026 — Výnosy a poplatky | Důchody.cz',
            'description' => 'Porovnejte všechny penzijní fondy v ČR. Výnosy za 1, 3 a 5 let, poplatky a počty účastníků na jednom místě.',
            'canonical'   => route('fondy.index'),
        ];

        return view('funds.index', [
            'funds'         => $funds,
            'fundsForTable' => $fundsForTable,
            'meta'          => $meta,
        ]);
    }

    public function show(string $slug): View
    {
        $fund = PensionFund::where('slug', $slug)->firstOrFail();

        $rating    = $this->rating($fund);
        $chartData = $this->chartData($fund);

        $meta = [
            'title'       => "{$fund->name} — Výnosy a poplatky | Důchody.cz",
            'description' => "Podrobné informace o fondu {$fund->name}: výnosy, poplatky a srovnání s ostatními fondy.",
            'canonical'   => route('fondy.show', $slug),
        ];

        return view('funds.show', compact('fund', 'rating', 'chartData', 'meta'));
    }

    /**
     * Hodnocení fondu 1–5 hvězd v rámci jeho kategorie.
     *
     * Metodika: vážený výnos (50 % pětiletý, 30 % tříletý, 20 % roční)
     * snížený o dvojnásobek ročního poplatku za správu. Fondy stejného
     * typu jsou seřazeny a rozděleny do pěti pásem.
     *
     * @return array{stars: int, score: float, rank: int, totalInCategory: int}
     */
    private function rating(PensionFund $fund): array
    {
        $categoryFunds = PensionFund::where('fund_type', $fund->fund_type)->get();

        $scores = $categoryFunds
            ->mapWithKeys(fn (PensionFund $f): array => [$f->slug => $this->score($f)])
            ->sortDesc();

        $rank  = $scores->keys()->search($fund->slug) + 1;
        $total = $scores->count();

        // 1. místo z N → percentil → hvězdy 1–5
        $percentile = 1 - (($rank - 1) / max($total, 1));
        $stars      = max(1, (int) ceil($percentile * 5));

        return [
            'stars'           => $stars,
            'score'           => round($this->score($fund), 2),
            'rank'            => $rank,
            'totalInCategory' => $total,
        ];
    }

    private function score(PensionFund $fund): float
    {
        return ($fund->return_5y ?? 0) * 0.5
            + ($fund->return_3y ?? 0) * 0.3
            + ($fund->return_1y ?? 0) * 0.2
            - $fund->fee_management * 2;
    }

    /**
     * Roční výnosy posledních 5 let odvozené z průměrů (1Y/3Y/5Y) + inflace.
     *
     * Přesná roční data nejsou v DB — aproximace: poslední rok = 1Y výnos,
     * předchozí 2 roky dorovnávají 3Y průměr, nejstarší 2 roky 5Y průměr.
     *
     * @return array{labels: array<int, int>, fundReturns: array<int, float>, inflation: array<int, float>, fundName: string}
     */
    private function chartData(PensionFund $fund): array
    {
        $r1 = $fund->return_1y ?? 0.0;
        $r3 = $fund->return_3y ?? $r1;
        $r5 = $fund->return_5y ?? $r3;

        $mid = round((3 * $r3 - $r1) / 2, 2);
        $old = round((5 * $r5 - 3 * $r3) / 2, 2);

        $years   = array_keys(self::INFLATION_BY_YEAR);
        $returns = [$old, $old, $mid, $mid, round($r1, 2)];

        return [
            'labels'      => $years,
            'fundReturns' => $returns,
            'inflation'   => array_values(self::INFLATION_BY_YEAR),
            'fundName'    => $fund->name,
        ];
    }
}
