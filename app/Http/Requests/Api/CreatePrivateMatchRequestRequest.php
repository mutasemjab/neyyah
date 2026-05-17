<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CreatePrivateMatchRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'matchmaker_id'       => 'required|exists:matchmakers,id',
            'package_id'          => 'nullable|exists:matchmaker_packages,id',
            'payment_reference'   => 'nullable|string|max:200',
            'personal_details'    => 'required|array',
            'partner_preferences' => 'required|array',
        ];
    }
}
