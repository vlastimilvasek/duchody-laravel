<?php

declare(strict_types=1);

namespace App\Http\Controllers\Calculator;

use App\Enums\Gender;
use App\Http\Controllers\Controller;
use App\Services\Pension\RetirementAge;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PensionAgeController extends Controller
{
    public function index(): View
    {
        $meta = [
            'title'       => 'Kalkulačka důchodového věku 2026 — Kdy půjdu do důchodu?',
            'description' => 'Zjistěte přesně, kdy půjdete do důchodu. Zadejte datum narození, pohlaví a počet dětí — výsledek okamžitě.',
            'canonical'   => route('kalkulacka.vek'),
        ];

        return view('calculator.vek', compact('meta'));
    }

    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'birth_date' => ['required', 'date', 'before:-15 years'],
            'gender'     => ['required', 'in:M,F'],
            'children'   => ['required', 'integer', 'min:0', 'max:10'],
        ]);

        $birthDate = Carbon::parse($validated['birth_date']);
        $gender    = Gender::from($validated['gender']);
        $children  = (int) $validated['children'];

        $age            = RetirementAge::calculate($birthDate, $gender, $children);
        $retirementDate = RetirementAge::retirementDate($birthDate, $gender, $children);
        $daysRemaining  = (int) now()->diffInDays($retirementDate, false);
        $yearsRemaining = round(max(0, $daysRemaining) / 365.25, 1);

        return response()->json([
            'age'             => $age,
            'retirementDate'  => $retirementDate->format('j. n. Y'),
            'daysRemaining'   => max(0, $daysRemaining),
            'yearsRemaining'  => $yearsRemaining,
            'isAlreadyPast'   => $daysRemaining < 0,
        ]);
    }
}
