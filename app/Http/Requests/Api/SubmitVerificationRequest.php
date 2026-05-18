<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SubmitVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_type'        => ['required', 'string', 'in:national_id,passport,residence'],
            'document_front' => ['required', 'file', 'image', 'max:10240', 'mimes:jpg,jpeg,png'],
            'document_back'  => ['nullable', 'file', 'image', 'max:10240', 'mimes:jpg,jpeg,png'],
            'selfie'         => ['nullable', 'file', 'image', 'max:10240', 'mimes:jpg,jpeg,png'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_type.required'        => 'نوع الوثيقة مطلوب.',
            'id_type.in'              => 'نوع الوثيقة غير صحيح.',
            'document_front.required' => 'صورة الواجهة الأمامية للوثيقة مطلوبة.',
            'document_front.image'    => 'يجب أن تكون صورة صالحة.',
            'document_front.max'      => 'حجم الصورة لا يتجاوز 10 ميغابايت.',
            'document_back.image'     => 'يجب أن تكون صورة صالحة.',
            'selfie.image'            => 'يجب أن تكون صورة صالحة.',
        ];
    }
}
