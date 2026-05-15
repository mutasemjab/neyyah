<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePrivacyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hide_from_contacts'    => ['sometimes', 'boolean'],
            'anonymous_browsing'    => ['sometimes', 'boolean'],
            'blur_images'           => ['sometimes', 'boolean'],
            'hide_real_name'        => ['sometimes', 'boolean'],
            'show_last_active'      => ['sometimes', 'boolean'],
            'allow_location_detect' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'hide_from_contacts.boolean'    => 'قيمة إخفاء من جهات الاتصال يجب أن تكون صحيحة أو خاطئة.',
            'anonymous_browsing.boolean'    => 'قيمة التصفح المجهول يجب أن تكون صحيحة أو خاطئة.',
            'blur_images.boolean'           => 'قيمة تمويه الصور يجب أن تكون صحيحة أو خاطئة.',
            'hide_real_name.boolean'        => 'قيمة إخفاء الاسم الحقيقي يجب أن تكون صحيحة أو خاطئة.',
            'show_last_active.boolean'      => 'قيمة عرض آخر نشاط يجب أن تكون صحيحة أو خاطئة.',
            'allow_location_detect.boolean' => 'قيمة السماح بتحديد الموقع يجب أن تكون صحيحة أو خاطئة.',
        ];
    }
}
