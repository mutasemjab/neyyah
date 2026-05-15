<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'regex:/^\+9627[789]\d{7}$/'],
            'code'  => ['required', 'string', 'digits:4'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'رقم الهاتف مطلوب.',
            'phone.regex'    => 'رقم الهاتف يجب أن يكون رقم جوال أردني صحيح.',
            'code.required'  => 'رمز التحقق مطلوب.',
            'code.digits'    => 'رمز التحقق يجب أن يتكون من 4 أرقام.',
        ];
    }
}
