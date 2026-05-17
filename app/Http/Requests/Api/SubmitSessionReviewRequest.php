<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SubmitSessionReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating'    => 'required|integer|min:1|max:5',
            'review_ar' => 'nullable|string|max:1000',
        ];
    }
}
