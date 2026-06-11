<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CalculatePensionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'birth_date'      => ['required', 'date', 'after:1936-01-01', 'before:-15 years'],
            'gender'          => ['required', 'in:M,F'],
            'children'        => ['required', 'integer', 'min:0', 'max:10'],
            'retirement_date' => ['required', 'date', 'after:birth_date'],
            'insurance_years' => ['required', 'integer', 'min:1', 'max:55'],
            'income_mode'     => ['required', 'in:average,yearly'],
            'average_monthly_income' => ['required_if:income_mode,average', 'nullable', 'numeric', 'min:0', 'max:9999999'],
            'yearly_income'   => ['required_if:income_mode,yearly', 'nullable', 'array'],
            'yearly_income.*' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'excluded_days'   => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:20000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'birth_date.required'      => 'Vyplňte prosím datum narození.',
            'birth_date.after'         => 'Kalkulačka podporuje ročníky od roku 1936.',
            'birth_date.before'        => 'Datum narození musí být alespoň 15 let v minulosti.',
            'gender.required'          => 'Vyberte prosím pohlaví.',
            'children.required'        => 'Vyplňte prosím počet vychovaných dětí.',
            'retirement_date.required' => 'Vyplňte prosím plánované datum odchodu do důchodu.',
            'retirement_date.after'    => 'Datum odchodu musí následovat po datu narození.',
            'insurance_years.required' => 'Vyplňte prosím dobu pojištění.',
            'insurance_years.min'      => 'Doba pojištění musí být alespoň 1 rok.',
            'insurance_years.max'      => 'Doba pojištění může být nejvýše 55 let.',
            'average_monthly_income.required_if' => 'Vyplňte prosím průměrný měsíční příjem.',
            'yearly_income.required_if'          => 'Vyplňte prosím příjmy po jednotlivých letech.',
        ];
    }
}
