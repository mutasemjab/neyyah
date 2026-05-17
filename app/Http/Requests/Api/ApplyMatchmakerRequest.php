<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ApplyMatchmakerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bio_ar'           => 'required|string|min:50|max:1000',
            'specializations'  => 'nullable|array',
            'specializations.*' => 'string|max:100',
            'years_experience' => 'required|integer|min:0|max:50',
        ];
    }
}
