<?php

declare(strict_types=1);

namespace App\Services\Pension\Data;

final class WageCoefficients
{
    /**
     * Koeficienty nárůstu vyměřovacích základů pro rok přiznání 2026.
     * Zdroj: ČSSZ kalkulačka (ilustrativní — ověřit s oficálním XLS).
     *
     * @var array<int, float>
     */
    public const FOR_2026 = [
        1986 => 18.0821,
        1987 => 17.6923,
        1988 => 16.9812,
        1989 => 15.8934,
        1990 => 13.2156,
        1991 => 8.6743,
        1992 => 7.1234,
        1993 => 5.8921,
        1994 => 5.1043,
        1995 => 4.3829,
        1996 => 3.8712,
        1997 => 3.5021,
        1998 => 3.2143,
        1999 => 3.0234,
        2000 => 2.8523,
        2001 => 2.6742,
        2002 => 2.5821,
        2003 => 2.4901,
        2004 => 2.3812,
        2005 => 2.2543,
        2006 => 2.1234,
        2007 => 1.9821,
        2008 => 1.8534,
        2009 => 1.7891,
        2010 => 1.7234,
        2011 => 1.6543,
        2012 => 1.6012,
        2013 => 1.5678,
        2014 => 1.5234,
        2015 => 1.4823,
        2016 => 1.4234,
        2017 => 1.3456,
        2018 => 1.2678,
        2019 => 1.1901,
        2020 => 1.1456,
        2021 => 1.0823,
        2022 => 1.0234,
        2023 => 1.0123,
        2024 => 1.0000,
        2025 => 1.0000,
    ];

    /**
     * Koeficienty pro daný rok přiznání důchodu.
     *
     * @return array<int, float>
     */
    public static function forYear(int $referenceYear): array
    {
        // V produkci: WageCoefficient::where('reference_year', $referenceYear)->pluck('coefficient', 'income_year')->toArray()
        return match ($referenceYear) {
            2026 => self::FOR_2026,
            default => self::FOR_2026, // fallback
        };
    }
}
