<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SaveCalculationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'input_params' => ['required', 'array'],
            'result'       => ['required', 'array'],
            'result.totalMonthly' => ['required', 'integer', 'min:0'],
        ];
    }
}
