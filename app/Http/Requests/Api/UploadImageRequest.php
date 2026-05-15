<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.required'  => 'الصورة مطلوبة.',
            'image.file'      => 'يجب أن يكون الملف صورة.',
            'image.mimes'     => 'صيغة الصورة يجب أن تكون jpg أو jpeg أو png أو webp.',
            'image.max'       => 'حجم الصورة يجب ألا يتجاوز 5 ميغابايت.',
        ];
    }
}
