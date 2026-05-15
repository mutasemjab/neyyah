<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'display_name'       => ['sometimes', 'string', 'max:60'],
            'birth_date'         => ['sometimes', 'date', 'before:-18 years'],
            'gender'             => ['sometimes', 'string', 'in:male,female'],
            'city'               => ['sometimes', 'string', 'max:60'],
            'bio'                => ['sometimes', 'string', 'max:500'],
            'religiosity_level'  => ['sometimes', 'string', 'in:very_religious,religious,somewhat_religious,not_religious'],
            'education_level'    => ['sometimes', 'string', 'in:less_than_high_school,high_school,diploma,bachelor,master,phd'],
            'income_range'       => ['sometimes', 'string', 'in:less_500,500_1000,1000_2000,2000_3000,more_3000'],
            'is_smoker'          => ['sometimes', 'boolean'],
            'marriage_timeline'  => ['sometimes', 'string', 'in:immediately,within_6_months,within_year,more_than_year'],
            'is_ready_for_marriage' => ['sometimes', 'boolean'],
            'latitude'           => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'longitude'          => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'firebase_uid'       => ['sometimes', 'nullable', 'string', 'max:128'],
            'interests'          => ['sometimes', 'array', 'max:20'],
            'interests.*'        => ['string', 'max:60'],
        ];
    }

    public function messages(): array
    {
        return [
            'display_name.max'        => 'الاسم يجب ألا يتجاوز 60 حرفاً.',
            'birth_date.before'       => 'يجب أن يكون عمرك 18 سنة أو أكثر.',
            'gender.in'               => 'الجنس يجب أن يكون ذكر أو أنثى.',
            'religiosity_level.in'    => 'مستوى التدين غير صحيح.',
            'education_level.in'      => 'المستوى التعليمي غير صحيح.',
            'income_range.in'         => 'نطاق الدخل غير صحيح.',
            'marriage_timeline.in'    => 'الإطار الزمني للزواج غير صحيح.',
        ];
    }
}
