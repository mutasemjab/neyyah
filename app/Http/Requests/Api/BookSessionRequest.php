<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class BookSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_date'      => 'required|date|after_or_equal:today',
            'start_time'        => 'required|date_format:H:i',
            'end_time'          => 'required|date_format:H:i|after:start_time',
            'meeting_type'      => 'required|in:voice,video,chat',
            'payment_reference' => 'nullable|string|max:200',
        ];
    }
}
