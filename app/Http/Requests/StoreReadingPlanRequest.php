<?php

namespace App\Http\Requests;

use App\Enums\ReadingPlanStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReadingPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'book_id' => ['required', 'exists:books,id', Rule::unique('reading_plans', 'book_id')->where(function ($query) {
                return $query->where('user_id', $this->user()->id)->where('status', '!=', ReadingPlanStatus::Completed->value);
                }),
            ],
            'target_date' => ['required', 'date', 'after_or_equal:today',],
        ];
    }

    public function messages(): array
    {
        return [
            'book_id.required' => '書籍を選択してください。',
            'book_id.exists' => '選択された書籍が存在しません。',
            'book_id.unique' => 'この書籍には既に進行中または計画中の読書計画が存在します。',
            'target_date.required' => '期日を入力してください。',
            'target_date.date' => '目標読了日は正しい日付形式で入力してください。',
            'target_date.after_or_equal' => '期日は今日以降の日付を指定してください。',
        ];
    }
}