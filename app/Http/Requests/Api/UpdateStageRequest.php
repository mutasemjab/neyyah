<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stage' => ['required', 'string', 'in:taaaruf,ihtimam,jiddiyya,family,khitba'],
        ];
    }

    public function messages(): array
    {
        return [
            'stage.required' => 'مرحلة المحادثة مطلوبة.',
            'stage.in'       => 'مرحلة المحادثة غير صحيحة.',
        ];
    }
}
