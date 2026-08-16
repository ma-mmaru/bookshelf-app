<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'max:20', 'unique:books,isbn'],
            'description' => ['nullable', 'string'],
            'published_date' => ['required', 'date'],
            'user_id' => ['required', 'integer','exists:users,id'],
            'genre_ids' => ['required', 'array', 'min:1'],
            'genre_ids.*' => ['integer', 'exists:genres,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'タイトルを入力してください。',
            'author.required' => '著者名を入力してください。',
            'isbn.required' => 'ISBNを入力してください。',
            'isbn.unique' => 'そのISBNは既に登録されています。',
            'published_date.required' => '出版日を入力してください。',
            'user_id.required' => '登録者IDを入力してください。',
            'user_id.exists' => '指定された登録者IDが存在しません。',
            'genre_ids.required' => 'ジャンルを1つ以上選択してください。',
            'genre_ids.*.exists' => '指定されたジャンルIDは存在しません。',
        ];
    }
}