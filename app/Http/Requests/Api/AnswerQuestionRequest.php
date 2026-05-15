<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class AnswerQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'answer_text' => ['required', 'string', 'min:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'answer_text.required' => 'نص الإجابة مطلوب.',
            'answer_text.min'      => 'الإجابة يجب أن تكون 20 حرفاً على الأقل.',
        ];
    }
}
