<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\validation\Rule;

class GenreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $genre = $this->route('genre');
        $genreId = $genre ? $genre->id : null;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('genres', 'name')->ignore($genreId),],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'ジャンル名を入力してください。',
            'name.unique' => 'そのジャンル名は既に登録されています。',
            'name.max' => 'ジャンル名は255文字以内で入力してください。',
        ];
    }
}