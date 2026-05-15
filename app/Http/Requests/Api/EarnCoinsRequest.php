<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class EarnCoinsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:ad,share,invite'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'نوع الكسب مطلوب.',
            'type.in'       => 'نوع الكسب يجب أن يكون: مشاهدة إعلان أو مشاركة أو دعوة.',
        ];
    }
}
