<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class DeleteDeviceTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'رمز الجهاز مطلوب.',
            'token.max'      => 'رمز الجهاز يجب ألا يتجاوز 500 حرف.',
        ];
    }
}
