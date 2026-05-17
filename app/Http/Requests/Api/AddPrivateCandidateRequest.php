<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AddPrivateCandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'candidate_user_id'  => 'nullable|exists:users,id',
            'candidate_details'  => 'required|array',
            'note_ar'            => 'nullable|string|max:1000',
        ];
    }
}
