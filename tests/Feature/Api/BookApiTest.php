<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    public function test_API経由で書籍一覧および検索ができる(): void
    {
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();
        $book->genres()->attach($genre->id);

        $response = $this->getJson("/api/v1/books?keyword={$book->title}&genre_id={$genre->id}");

        $response->assertStatus(200);
    }

    public function test_API経由で書籍を作成できる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $data = [
            'title' => 'API書籍タイトル',
            'author' => 'API著者',
            'isbn' => '9784798199999',
            'published_date' => '2024-05-01',
            'user_id' => $user->id,
            'genre_ids' => [$genre->id],
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(201)->assertJsonPath('data.title', 'API書籍タイトル');

        $this->assertDatabaseHas('books', ['isbn' => '9784798199999']);
    }

    public function test_APIでの書籍作成失敗時に422ステータスを返す(): void
    {
        $response = $this->postJson('/api/v1/books', []);

        $response->assertStatus(422)->assertJsonValidationErrors(['title', 'author', 'isbn', 'published_date', 'user_id', 'genre_ids']);
    }

    public function test_API経由で書籍を更新できる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create(['user_id' => $user->id]);

        $data = [
            'title' => 'API更新後タイトル',
            'author' => $book->author,
            'isbn' => $book->isbn,
            'published_date' => $book->published_date,
            'genre_ids' => [$genre->id],
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => 'API更新後タイトル']);
    }
}