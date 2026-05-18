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
            'show_age'                   => ['sometimes', 'boolean'],
            'show_city'                  => ['sometimes', 'boolean'],
            'show_photos_to_matches_only'=> ['sometimes', 'boolean'],
            'hide_from_contacts'         => ['sometimes', 'boolean'],
            'anonymous_browsing'         => ['sometimes', 'boolean'],
            'show_last_active'           => ['sometimes', 'boolean'],
            'blur_images'                => ['sometimes', 'boolean'],
            'hide_real_name'             => ['sometimes', 'boolean'],
            'allow_location_detect'      => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'show_age.boolean'                    => 'قيمة إظهار العمر يجب أن تكون صحيحة أو خاطئة.',
            'show_city.boolean'                   => 'قيمة إظهار المدينة يجب أن تكون صحيحة أو خاطئة.',
            'show_photos_to_matches_only.boolean' => 'قيمة إظهار الصور للمطابقات فقط يجب أن تكون صحيحة أو خاطئة.',
            'hide_from_contacts.boolean'          => 'قيمة إخفاء من جهات الاتصال يجب أن تكون صحيحة أو خاطئة.',
            'anonymous_browsing.boolean'          => 'قيمة التصفح المجهول يجب أن تكون صحيحة أو خاطئة.',
            'show_last_active.boolean'            => 'قيمة عرض آخر نشاط يجب أن تكون صحيحة أو خاطئة.',
            'blur_images.boolean'                 => 'قيمة تمويه الصور يجب أن تكون صحيحة أو خاطئة.',
            'hide_real_name.boolean'              => 'قيمة إخفاء الاسم الحقيقي يجب أن تكون صحيحة أو خاطئة.',
            'allow_location_detect.boolean'       => 'قيمة السماح بتحديد الموقع يجب أن تكون صحيحة أو خاطئة.',
        ];
    }
}
