<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class DeviceTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token'    => ['required', 'string', 'max:500'],
            'platform' => ['required', 'string', 'in:android,ios'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required'    => 'رمز الجهاز مطلوب.',
            'token.max'         => 'رمز الجهاز يجب ألا يتجاوز 500 حرف.',
            'platform.required' => 'نوع المنصة مطلوب.',
            'platform.in'       => 'نوع المنصة يجب أن يكون android أو ios.',
        ];
    }
}
