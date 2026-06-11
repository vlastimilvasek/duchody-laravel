<?php

declare(strict_types=1);

namespace App\Http\Controllers\Seo;

use App\Enums\Gender;
use App\Http\Controllers\Controller;
use App\Services\Pension\RetirementAge;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

final class RocnikController extends Controller
{
    public const MIN_YEAR = 1940;

    public const MAX_YEAR = 1980;

    public function show(int $rok): View
    {
        abort_unless($rok >= self::MIN_YEAR && $rok <= self::MAX_YEAR, 404);

        $data = Cache::remember("duchod.rocnik.{$rok}", now()->addYear(), fn (): array => $this->buildData($rok));

        $meta = [
            'title'       => "Důchod pro ročník {$rok} — Kdy a v kolika letech? | Důchody.cz",
            'description' => "Důchodový věk pro ročník {$rok}: muži, ženy podle počtu dětí, rok odchodu do důchodu a odpovědi na nejčastější otázky.",
            'canonical'   => route('duchod.rocnik', $rok),
        ];

        return view('seo.rocnik', array_merge($data, compact('meta')));
    }

    /** @return array<string, mixed> */
    private function buildData(int $rok): array
    {
        $birthDate = Carbon::create($rok, 7, 1);

        $male = RetirementAge::calculate($birthDate, Gender::Male, 0);

        $table = [
            [
                'label'          => 'Muž',
                'age'            => $male,
                'retirementYear' => $rok + $male['years'] + (int) ($male['months'] > 6),
            ],
        ];

        foreach ([0, 1, 2, 3, 4] as $children) {
            $age     = RetirementAge::calculate($birthDate, Gender::Female, $children);
            $table[] = [
                'label'          => 'Žena — ' . match ($children) {
                    0       => 'bezdětná',
                    1       => '1 dítě',
                    4       => '4 a více dětí',
                    default => "{$children} děti",
                },
                'age'            => $age,
                'retirementYear' => $rok + $age['years'] + (int) ($age['months'] > 6),
            ];
        }

        // Průměrná doba pojištění generace — orientačně dle ČSSZ (starší ročníky mívají delší kariéry)
        $insuranceYears = $rok <= 1955 ? 44 : ($rok <= 1965 ? 43 : 42);

        $maleLabel = $this->ageLabel($male);

        $faq = [
            [
                'q' => "V kolika letech půjde do důchodu muž narozený v roce {$rok}?",
                'a' => "Muž narozený v roce {$rok} dosáhne důchodového věku v {$maleLabel}, tedy přibližně v roce {$table[0]['retirementYear']}.",
            ],
            [
                'q' => "Snižuje se důchodový věk ženám ročníku {$rok} za vychované děti?",
                'a' => $rok <= 1965
                    ? "Ano. Ženy narozené v roce {$rok} mají důchodový věk snížený o 4 měsíce za každé vychované dítě (maximálně za čtyři děti)."
                    : "U žen narozených po roce 1965 se důchodový věk za děti již nesnižuje — výchova dětí se projeví bonusem 500 Kč měsíčně (výchovné) k důchodu.",
            ],
            [
                'q' => "Kdy může ročník {$rok} odejít do předčasného důchodu?",
                'a' => 'Do předčasného důchodu lze odejít nejdříve 3 roky před dosažením řádného důchodového věku, při získání alespoň 40 let doby pojištění. Krácení činí 1,5 % výpočtového základu za každých započatých 90 dní předčasnosti.',
            ],
            [
                'q' => "Kolik let pojištění potřebuje ročník {$rok} pro nárok na důchod?",
                'a' => 'Pro nárok na starobní důchod je potřeba alespoň 35 let doby pojištění (včetně náhradních dob). Průměrná doba pojištění této generace je přibližně ' . $insuranceYears . ' let.',
            ],
        ];

        $related = array_values(array_filter(
            range($rok - 5, $rok + 5),
            fn (int $r): bool => $r !== $rok && $r >= self::MIN_YEAR && $r <= self::MAX_YEAR,
        ));

        return [
            'rok'            => $rok,
            'male'           => $male,
            'maleLabel'      => $maleLabel,
            'table'          => $table,
            'insuranceYears' => $insuranceYears,
            'faq'            => $faq,
            'related'        => $related,
        ];
    }

    /** @param array{years: int, months: int} $age */
    private function ageLabel(array $age): string
    {
        $label = "{$age['years']} let";
        if ($age['months'] > 0) {
            $label .= " a {$age['months']} " . ($age['months'] === 1 ? 'měsíc' : ($age['months'] <= 4 ? 'měsíce' : 'měsíců'));
        }

        return $label;
    }
}
