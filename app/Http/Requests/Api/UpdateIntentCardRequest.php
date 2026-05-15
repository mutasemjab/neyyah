<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIntentCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'children_intent'         => ['sometimes', 'nullable', 'string', 'in:yes,no,open'],
            'open_to_working_partner' => ['sometimes', 'boolean'],
            'living_preference'       => ['sometimes', 'nullable', 'string', 'in:same_city,any_city,same_country,any_country'],
            'target_timeline'         => ['sometimes', 'nullable', 'string', 'in:immediately,within_6_months,within_year,more_than_year'],
            'additional_notes'        => ['sometimes', 'nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'children_intent.in'      => 'قيمة نية الأطفال غير صحيحة.',
            'living_preference.in'    => 'تفضيل السكن غير صحيح.',
            'target_timeline.in'      => 'الإطار الزمني المستهدف غير صحيح.',
            'additional_notes.max'    => 'الملاحظات الإضافية يجب ألا تتجاوز 1000 حرف.',
        ];
    }
}
