<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SubscribeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'package_id'        => ['required', 'string', 'exists:subscription_packages,id'],
            'payment_reference' => ['sometimes', 'nullable', 'string', 'max:200'],
        ];
    }

    public function messages(): array
    {
        return [
            'package_id.required' => 'معرف الباقة مطلوب.',
            'package_id.exists'   => 'الباقة المحددة غير موجودة.',
        ];
    }
}
