<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreateMatchmakerPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_ar'               => 'required|string|max:150',
            'price'                 => 'required|numeric|min:0',
            'duration_days'         => 'required|integer|min:1',
            'candidate_limit'       => 'required|integer|min:1',
            'consultation_sessions' => 'nullable|integer|min:0',
            'priority_support'      => 'nullable|boolean',
            'description_ar'        => 'nullable|string|max:1000',
            'sort_order'            => 'nullable|integer|min:0',
        ];
    }
}
