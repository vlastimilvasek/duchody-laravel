<?php

declare(strict_types=1);

namespace App\Http\Controllers\Calculator;

use App\Enums\Gender;
use App\Http\Controllers\Controller;
use App\Http\Requests\CalculatePensionRequest;
use App\Http\Requests\SaveCalculationRequest;
use App\Models\SavedCalculation;
use App\Services\Pension\PensionCalculator;
use App\Services\Pension\RetirementAge;
use App\Services\Pension\DTO\PensionParams;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

final class PensionAmountController extends Controller
{
    public function __construct(
        private readonly PensionCalculator $calculator,
    ) {}

    public function index(): View
    {
        $meta = [
            'title'       => 'Výpočet výše důchodu 2026 — Kolik budu mít důchod?',
            'description' => 'Orientační výpočet výše starobního důchodu. Zadejte příjmy a doby pojištění — zobrazíme základní i procentní výměru.',
            'canonical'   => route('kalkulacka.vyse'),
        ];

        return view('calculator.vyse', compact('meta'));
    }

    public function calculate(CalculatePensionRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $birthDate      = Carbon::parse($validated['birth_date']);
        $retirementDate = Carbon::parse($validated['retirement_date']);
        $gender         = Gender::from($validated['gender']);
        $children       = (int) $validated['children'];

        $yearlyIncome = $this->resolveYearlyIncome($validated, $birthDate, $retirementDate);

        $params = new PensionParams(
            birthDate:      $birthDate,
            gender:         $gender,
            children:       $children,
            retirementDate: $retirementDate,
            yearlyIncome:   $yearlyIncome,
            insuranceYears: (int) $validated['insurance_years'],
            excludedDays:   (float) ($validated['excluded_days'] ?? 0),
        );

        $result = $this->calculator->calculate($params);

        // Kontext pro frontend: řádný důchodový věk + předčasnost/přesluhování
        $normalDate = RetirementAge::retirementDate($birthDate, $gender, $children);
        $normalAge  = RetirementAge::calculate($birthDate, $gender, $children);
        $earlyDays  = (int) $retirementDate->diffInDays($normalDate, false);

        return response()->json([
            ...$result->toArray(),
            'normalRetirementDate' => $normalDate->toDateString(),
            'normalRetirementAge'  => $normalAge,
            'earlyDays'            => $earlyDays,
        ]);
    }

    public function pdf(CalculatePensionRequest $request): Response
    {
        $validated = $request->validated();

        $birthDate      = Carbon::parse($validated['birth_date']);
        $retirementDate = Carbon::parse($validated['retirement_date']);
        $gender         = Gender::from($validated['gender']);
        $children       = (int) $validated['children'];

        $params = new PensionParams(
            birthDate:      $birthDate,
            gender:         $gender,
            children:       $children,
            retirementDate: $retirementDate,
            yearlyIncome:   $this->resolveYearlyIncome($validated, $birthDate, $retirementDate),
            insuranceYears: (int) $validated['insurance_years'],
            excludedDays:   (float) ($validated['excluded_days'] ?? 0),
        );

        $result    = $this->calculator->calculate($params);
        $normalAge = RetirementAge::calculate($birthDate, $gender, $children);

        $pdf = Pdf::loadView('pdf.pension-result', [
            'result'         => $result,
            'birthDate'      => $birthDate,
            'retirementDate' => $retirementDate,
            'gender'         => $gender,
            'children'       => $children,
            'normalAge'      => $normalAge,
            'generatedAt'    => now(),
        ]);

        return $pdf->download('vypocet-duchodu-duchody-cz.pdf');
    }

    public function save(SaveCalculationRequest $request): JsonResponse
    {
        $calculation = SavedCalculation::create([
            'session_id'   => $request->session()->getId(),
            'calc_type'    => 'vyse',
            'input_params' => $request->validated('input_params'),
            'result'       => $request->validated('result'),
        ]);

        return response()->json(['id' => $calculation->id], 201);
    }

    /**
     * Roční příjmy buď z průměrného platu, nebo zadané rok po roku.
     *
     * @param array<string, mixed> $validated
     * @return array<int, float>
     */
    private function resolveYearlyIncome(array $validated, Carbon $birthDate, Carbon $retirementDate): array
    {
        $startYear = max(1986, $birthDate->year + 18);
        $endYear   = $retirementDate->year - 1;

        if ($validated['income_mode'] === 'yearly') {
            $yearlyIncome = [];
            foreach ((array) ($validated['yearly_income'] ?? []) as $year => $amount) {
                $year = (int) $year;
                if ($year >= 1986 && $year <= $endYear && (float) $amount > 0) {
                    $yearlyIncome[$year] = (float) $amount;
                }
            }

            return $yearlyIncome;
        }

        $monthlyIncome = (float) $validated['average_monthly_income'];
        $yearlyIncome  = [];
        for ($year = $startYear; $year <= $endYear; $year++) {
            $yearlyIncome[$year] = $monthlyIncome * 12;
        }

        return $yearlyIncome;
    }
}
