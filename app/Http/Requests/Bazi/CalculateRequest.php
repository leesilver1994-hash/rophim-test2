<?php

namespace App\Http\Requests\Bazi;

use Illuminate\Foundation\Http\FormRequest;

class CalculateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'birth_date' => ['required', 'date'],
            'birth_time' => ['required', 'date_format:H:i'],
            'gender' => ['required', 'in:male,female,other'],
            'timezone' => ['nullable', 'string'],
        ];
    }
}
