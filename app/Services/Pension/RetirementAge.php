<?php

declare(strict_types=1);

namespace App\Services\Pension;

use App\Enums\Gender;
use Carbon\Carbon;

final class RetirementAge
{
    /**
     * Přesná tabulka důchodových věků dle MPSV (§32 ZDP + příloha zákona 155/1995 Sb.)
     * Muži — pro ročníky 1936–1977.
     *
     * @var array<int, array{years: int, months: int}>
     */
    private const MALE_TABLE = [
        1936 => ['years' => 60, 'months' => 0],
        1937 => ['years' => 60, 'months' => 6],
        1938 => ['years' => 61, 'months' => 0],
        1939 => ['years' => 61, 'months' => 6],
        1940 => ['years' => 62, 'months' => 0],
        1941 => ['years' => 62, 'months' => 6],
        1942 => ['years' => 63, 'months' => 0],
        1943 => ['years' => 63, 'months' => 6],
        1944 => ['years' => 64, 'months' => 0],
        1945 => ['years' => 64, 'months' => 0],
        1946 => ['years' => 64, 'months' => 2],
        1947 => ['years' => 64, 'months' => 4],
        1948 => ['years' => 64, 'months' => 6],
        1949 => ['years' => 64, 'months' => 8],
        1950 => ['years' => 64, 'months' => 10],
        1951 => ['years' => 65, 'months' => 0],
        1952 => ['years' => 65, 'months' => 0],
        1953 => ['years' => 65, 'months' => 0],
        1954 => ['years' => 65, 'months' => 0],
        1955 => ['years' => 65, 'months' => 0],
        1956 => ['years' => 65, 'months' => 2],
        1957 => ['years' => 65, 'months' => 4],
        1958 => ['years' => 65, 'months' => 6],
        1959 => ['years' => 65, 'months' => 8],
        1960 => ['years' => 65, 'months' => 10],
        1961 => ['years' => 66, 'months' => 0],
        1962 => ['years' => 66, 'months' => 2],
        1963 => ['years' => 66, 'months' => 4],
        1964 => ['years' => 66, 'months' => 6],
        1965 => ['years' => 64, 'months' => 8],
        1966 => ['years' => 65, 'months' => 0],
        1967 => ['years' => 65, 'months' => 2],
        1968 => ['years' => 65, 'months' => 4],
        1969 => ['years' => 65, 'months' => 6],
        1970 => ['years' => 65, 'months' => 8],
        1971 => ['years' => 65, 'months' => 10],
        1972 => ['years' => 66, 'months' => 0],
        1973 => ['years' => 65, 'months' => 8],
    ];

    /**
     * Tabulka pro ženy bezdětné nebo s 1 dítětem (reference) — ročníky 1936–1965.
     * Ženy s více dětmi mají snížený věk.
     *
     * @var array<int, array{years: int, months: int}>
     */
    private const FEMALE_TABLE_0_CHILDREN = [
        1936 => ['years' => 53, 'months' => 0],
        1937 => ['years' => 53, 'months' => 4],
        1938 => ['years' => 53, 'months' => 8],
        1939 => ['years' => 54, 'months' => 0],
        1940 => ['years' => 54, 'months' => 4],
        1941 => ['years' => 54, 'months' => 8],
        1942 => ['years' => 55, 'months' => 0],
        1943 => ['years' => 55, 'months' => 4],
        1944 => ['years' => 55, 'months' => 8],
        1945 => ['years' => 56, 'months' => 0],
        1946 => ['years' => 56, 'months' => 4],
        1947 => ['years' => 56, 'months' => 8],
        1948 => ['years' => 57, 'months' => 0],
        1949 => ['years' => 57, 'months' => 4],
        1950 => ['years' => 57, 'months' => 8],
        1951 => ['years' => 58, 'months' => 0],
        1952 => ['years' => 58, 'months' => 4],
        1953 => ['years' => 58, 'months' => 8],
        1954 => ['years' => 59, 'months' => 0],
        1955 => ['years' => 59, 'months' => 4],
        1956 => ['years' => 59, 'months' => 8],
        1957 => ['years' => 60, 'months' => 0],
        1958 => ['years' => 60, 'months' => 4],
        1959 => ['years' => 60, 'months' => 8],
        1960 => ['years' => 61, 'months' => 0],
        1961 => ['years' => 61, 'months' => 4],
        1962 => ['years' => 61, 'months' => 8],
        1963 => ['years' => 62, 'months' => 0],
        1964 => ['years' => 62, 'months' => 4],
        1965 => ['years' => 62, 'months' => 8],
    ];

    /**
     * Snížení věku žen za vychované děti (2–4 děti).
     * 1 dítě = −4 měsíce, 2 děti = −8, 3 děti = −12, 4+ = −16 (max. na 53 let).
     *
     * @var array<int, int>
     */
    private const FEMALE_CHILD_REDUCTION_MONTHS = [
        0 => 0,
        1 => 4,
        2 => 8,
        3 => 12,
        4 => 16,
    ];

    /**
     * Výpočet důchodového věku dle vzorce pro ročníky 1974–1988:
     *   65 let + 8 měsíců + (rok_narození − 1973) měsíců
     *
     * @return array{years: int, months: int}
     */
    public static function byFormula(int $birthYear): array
    {
        if ($birthYear > 1988) {
            return ['years' => 67, 'months' => 0];
        }

        if ($birthYear < 1974) {
            return ['years' => 65, 'months' => 8];
        }

        $extraMonths = $birthYear - 1973;
        $totalMonths = 65 * 12 + 8 + $extraMonths;

        return [
            'years'  => intdiv($totalMonths, 12),
            'months' => $totalMonths % 12,
        ];
    }

    /**
     * Plný výpočet důchodového věku.
     * Zohledňuje pohlaví, počet vychovaných dětí a přesné tabulky MPSV.
     *
     * @return array{years: int, months: int}
     */
    public static function calculate(Carbon $birthDate, Gender $gender, int $children): array
    {
        $year = $birthDate->year;

        if ($gender === Gender::Male) {
            return self::calculateMale($year);
        }

        return self::calculateFemale($year, $children);
    }

    /**
     * Výpočet data odchodu do důchodu z data narození a důchodového věku.
     */
    public static function retirementDate(Carbon $birthDate, Gender $gender, int $children): Carbon
    {
        $age = self::calculate($birthDate, $gender, $children);

        return $birthDate->copy()
            ->addYears($age['years'])
            ->addMonths($age['months']);
    }

    /** @return array{years: int, months: int} */
    private static function calculateMale(int $birthYear): array
    {
        if (isset(self::MALE_TABLE[$birthYear])) {
            return self::MALE_TABLE[$birthYear];
        }

        // 1974–1988: vzorec
        if ($birthYear >= 1974 && $birthYear <= 1988) {
            return self::byFormula($birthYear);
        }

        // 1989+: 67 let
        return ['years' => 67, 'months' => 0];
    }

    /** @return array{years: int, months: int} */
    private static function calculateFemale(int $birthYear, int $children): array
    {
        $children = min($children, 4);

        if (isset(self::FEMALE_TABLE_0_CHILDREN[$birthYear])) {
            $base      = self::FEMALE_TABLE_0_CHILDREN[$birthYear];
            $reduction = self::FEMALE_CHILD_REDUCTION_MONTHS[$children] ?? 16;
            return self::subtractMonths($base, $reduction);
        }

        // Ročníky 1966+: stejný vzorec jako muži (ženy od 1966 mají vyrovnávání věku)
        if ($birthYear >= 1966 && $birthYear <= 1973) {
            $extraMonths = ($birthYear - 1965) * 4; // +4 měsíce za každý ročník od 1965
            $baseMonths  = 62 * 12 + 8;
            $totalMonths = $baseMonths + $extraMonths;
            $base        = ['years' => intdiv($totalMonths, 12), 'months' => $totalMonths % 12];
            $reduction   = self::FEMALE_CHILD_REDUCTION_MONTHS[$children] ?? 16;
            return self::subtractMonths($base, $reduction);
        }

        if ($birthYear >= 1974 && $birthYear <= 1988) {
            $base      = self::byFormula($birthYear);
            $reduction = 0; // od ročníku 1974 se věk žen nevyrovnává za děti
            return self::subtractMonths($base, $reduction);
        }

        return ['years' => 67, 'months' => 0];
    }

    /**
     * @param array{years: int, months: int} $age
     * @return array{years: int, months: int}
     */
    private static function subtractMonths(array $age, int $months): array
    {
        $totalMonths = $age['years'] * 12 + $age['months'] - $months;
        $totalMonths = max($totalMonths, 53 * 12); // minimum 53 let

        return [
            'years'  => intdiv($totalMonths, 12),
            'months' => $totalMonths % 12,
        ];
    }
}
