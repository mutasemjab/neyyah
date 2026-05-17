<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ApplyConsultantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title_ar'          => 'required|string|max:200',
            'specializations'   => 'nullable|array',
            'specializations.*' => 'string|max:100',
            'years_experience'  => 'required|integer|min:0|max:50',
            'session_price'     => 'required|numeric|min:0',
            'meeting_types'     => 'required|array|min:1',
            'meeting_types.*'   => 'in:voice,video,chat',
            'bio_ar'            => 'required|string|min:50|max:2000',
        ];
    }
}
