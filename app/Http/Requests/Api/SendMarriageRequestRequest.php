<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SendMarriageRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to_user_id'            => ['required', 'string', 'exists:users,id'],
            'reason_for_interest'   => ['required', 'string', 'min:50'],
            'life_goals'            => ['required', 'string', 'min:50'],
            'marriage_expectations' => ['required', 'string', 'min:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'to_user_id.required'            => 'معرف المستخدم مطلوب.',
            'to_user_id.exists'              => 'المستخدم المحدد غير موجود.',
            'reason_for_interest.required'   => 'سبب الاهتمام مطلوب.',
            'reason_for_interest.min'        => 'سبب الاهتمام يجب أن يكون 50 حرفاً على الأقل.',
            'life_goals.required'            => 'الأهداف في الحياة مطلوبة.',
            'life_goals.min'                 => 'الأهداف في الحياة يجب أن تكون 50 حرفاً على الأقل.',
            'marriage_expectations.required' => 'توقعات الزواج مطلوبة.',
            'marriage_expectations.min'      => 'توقعات الزواج يجب أن تكون 50 حرفاً على الأقل.',
        ];
    }
}
