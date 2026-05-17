<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateMatchmakerPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gender'            => 'required|in:male,female',
            'age_from'          => 'required|integer|min:18|max:80',
            'age_to'            => 'required|integer|min:18|max:80|gte:age_from',
            'city'              => 'nullable|string|max:100',
            'nationality_ar'    => 'nullable|string|max:100',
            'profession_ar'     => 'nullable|string|max:150',
            'religiosity_level' => 'nullable|string|max:50',
            'education_level'   => 'nullable|string|max:50',
            'bio_ar'            => 'required|string|min:30|max:1000',
            'requirements_ar'   => 'required|string|min:30|max:1000',
        ];
    }
}
