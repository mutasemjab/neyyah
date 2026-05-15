<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMatchFiltersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'min_age'            => ['sometimes', 'integer', 'min:18', 'max:80'],
            'max_age'            => ['sometimes', 'integer', 'min:18', 'max:80', 'gte:min_age'],
            'cities'             => ['sometimes', 'array'],
            'cities.*'           => ['string', 'max:60'],
            'religiosity_levels' => ['sometimes', 'array'],
            'religiosity_levels.*' => ['string', 'in:very_religious,religious,somewhat_religious,not_religious'],
            'education_levels'   => ['sometimes', 'array'],
            'education_levels.*' => ['string', 'in:less_than_high_school,high_school,diploma,bachelor,master,phd'],
            'no_smokers'         => ['sometimes', 'boolean'],
            'max_distance_km'    => ['sometimes', 'nullable', 'integer', 'min:1', 'max:500'],
            'marriage_timelines' => ['sometimes', 'array'],
            'marriage_timelines.*' => ['string', 'in:immediately,within_6_months,within_year,more_than_year'],
        ];
    }

    public function messages(): array
    {
        return [
            'min_age.min'              => 'الحد الأدنى للعمر يجب أن يكون 18 سنة على الأقل.',
            'max_age.gte'              => 'الحد الأقصى للعمر يجب أن يكون أكبر من أو يساوي الحد الأدنى.',
            'religiosity_levels.*.in'  => 'مستوى التدين غير صحيح.',
            'education_levels.*.in'    => 'المستوى التعليمي غير صحيح.',
            'marriage_timelines.*.in'  => 'الإطار الزمني للزواج غير صحيح.',
        ];
    }
}
